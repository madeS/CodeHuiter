<?php

namespace CodeHuiter\Pattern\Modules\Auth\Controllers;

use CodeHuiter\Exceptions\TagException;
use CodeHuiter\Pattern\Controllers\Base\BaseController;
use CodeHuiter\Pattern\Modules\Auth\AuthService;

class AuthController extends BaseController
{
    /**
     * @return bool|void
     */
    public function index()
    {
        $this->initWithAuth(false);
        if ($this->auth->user->id) {
            $redirectUrl = $this->request->getGet('url');
            if ($redirectUrl) {
                if (strpos($redirectUrl, 'http') === 0) {
                    // @todo low check if domain in allowed
                    $this->response->location('/', true);
                } else {
                    $this->response->location($redirectUrl, true);
                }
            } else {
                $this->response->location('/', true);
            }
            return;
        }
        if ($this->request->isMjsaAJAX() && $this->request->getGet('in_popup')) {
            $this->data['in_popup'] = true;
            $this->mjsa->openPopupWithData(
                $this->response->render($this->auth->getViewsPath() . 'login', $this->data, true),
                'authPopup',
                ['maxWidth' => 600, 'close' => true,]
            );
        } else {
            $this->render($this->auth->getViewsPath() . 'login');
        }
        return;
    }

    public function register()
    {
        $this->initWithAuth(false);
        $connectUi = ($this->auth->user->id) ? $this->auth->user : null;

        $data = $this->auth->registerByEmailValidator($this->mjsa, $_POST, [], $connectUi);
        if (!$data) {
            return false;
        }

        try {
            $this->auth->registerByEmail($data['email'], $data['password'], $data['email'], $connectUi);
        } catch (TagException $exception) {
            if ($exception->getTag() !== AuthService::AUTH_EVENT_EXCEPTION_TAG) {
                throw $exception;
            }
            $successMessage = '';
            $errorMessage = $exception->getMessage();
            switch ($exception->getCode()) {
                case AuthService::ERROR_REGISTER_EMAIL_TAKEN:
                    $this->mjsa->incorrect('email');
                    break;
                case AuthService::ERROR_REGISTER_LOGIN_TAKEN:
                    $this->mjsa->incorrect('login');
                    break;
                case AuthService::ERROR_REGISTER_DENIED:
                    break;
                case AuthService::ERROR_LOGIN_PASSWORD_WRONG:
                    $this->mjsa->incorrect('password');
                    break;
                case AuthService::ERROR_LOGIN_EMAIL_CONF_SENT:
                    $successMessage = $errorMessage;
                    $errorMessage = '';
                    $this->mjsa->formReplace($successMessage);
                    break;
                default:
                    $errorMessage .= " Code [{$exception->getCode()}]";
            }
            if ($errorMessage) {
                $this->mjsa->errorMessage($errorMessage);
            }
            if ($successMessage) {
                $this->mjsa->successMessage($successMessage);
            }
            return false;
        }

        $this->mjsa->closePopups();
        $this->mjsa->reload();
    }

    public function logout()
    {
        $this->initWithAuth(false);
        $this->auth->resetSig($this->auth->user);
        $this->response->location($_SERVER['HTTP_REFERER'],true);
    }











    public function set_language($language = ''){
        setcookie('language', $language, time() + 3600*30, '/');
        $this->mm->mjsaPrintEvent(array(
            /* 'success'=> 'Язык изменен', */ 'reload' => true, 'closePopups' => true,
        ));
    }

    public function login(){
        $this->mm->request_type = 'mjsa_ajax';
        $this->initWithAuth(false);
        $pdata = $this->mm->mjsaValidator($_POST, array(
            'logemail' => array('required' => true, 'required_text' => lang('mauth.auth.logemail_need')),
            'password' => array('required' => true, 'required_text' => lang('mauth.auth.password_need')),
        ));
        if ($this->mauth->loginPass($pdata['logemail'], $pdata['password']) === false){
            return $this->mm->mjsaPrintError($this->mauth->getErrorMessage());
        }
        echo '<script>mjsa.scrollPopup.closeAll();'
            . 'if($(".regauth_form_container .continue_url").val()) location.reload();'
            . 'else mjsa.bodyUpdate()</script>';
    }

    public function confirm_email(){
        $this->initWithAuth(false);
        if (!isset($_GET['user_id']) || ($_GET['user_id'] == '')
            || !isset($_GET['token']) || ($_GET['token'] == '')) {
            $this->data['h2'] = lang('mauth.email_token.broken_link.title');
            $this->data['p'] = lang('mauth.email_token.broken_link.p1');
            $this->data['content_tpl'] = 'mop/text_page.tpl.php';
            $this->load->view('main.tpl.php',$this->data);
            return false;
        }
        if($this->mauth->confirmEmailToken($_GET['user_id'],$_GET['token'])){
            $this->mm->location($this->links->userSettings());
        } else {
            $this->data['h2'] = lang('mauth.email_token.incorrect_link.title');
            $this->data['p'] = lang('mauth.email_token.incorrect_link.p1').$this->mauth->getErrorMessage();
            $this->data['content_tpl'] = 'mop/text_page.tpl.php';
            $this->load->view('main.tpl.php',$this->data);
            return false;
        }
    }

    public function user_edit_submit(){
        $this->mm->request_type = 'mjsa_ajax';
        if (!$this->initWithAuth(true)) return false;
        if ($this->mm->g($_POST['birthday_day']) && $this->mm->g($_POST['birthday_month']) && $this->mm->g($_POST['birthday_year'])) {
            $_POST['birthday'] = $_POST['birthday_year'].'-'.$_POST['birthday_month'].'-'.$_POST['birthday_day'];
        }
        $success = $this->musers->setUserInfo($this->data['ui'], $_POST);
        if (!$success) return $this->mm->mjsaPrintError($this->musers->getErrorMessage());
        $this->mm->mjsaPrintEvent(array(
            'success' => lang('musers:user_info_changed'), 'reload' => true, 'closePopups' => true,
        ));
    }
    public function user_edit_logemail_submit(){
        $this->mm->request_type = 'mjsa_ajax';
        if (!$this->initWithAuth(true)) return false;
        $registered = $this->mauth->registerUserByEmail(
            $this->mm->g($_POST['email']),
            $this->mm->g($_POST['password']),
            $this->mm->g($_POST['login']),
            $this->mm->g($_POST['name']),
            $this->data['ui']
        );
        if ($registered === false){
            return $this->mm->mjsaPrintError($this->mauth->getErrorMessage());
        }
        if ($registered === 'SENDED'){
            return $this->mm->mjsaPrintEvent(array(
                'redirect' => '/auth/email_conf_sended',
            ));
        }
        if ($registered === 'LOGINED'){
            // не бывает в данном случае
            return $this->mm->mjsaPrintError('LOGINED'); // ?? что это
        }
        $this->mm->mjsaPrintEvent(array(
            'success' => lang('musers:user_info_changed'), 'reload' => true, 'closePopups' => true,
        ));
        //[false][LOGINED][SENDED][REGISTERED/or/$ui]
    }
    public function user_edit_password_submit(){
        $this->mm->request_type = 'mjsa_ajax';
        if (!$this->initWithAuth(true)) return false;
        $pdata = $this->mm->mjsaValidator($_POST, array(
            'password' => array('required' => true, 'required_text' => lang('mauth.recover.need_old_password')),
            'newpassword' => array('required' => true, 'required_text' => lang('mauth.recover.need_password')),
            'newpassword_conf' => array('required' => true, 'required_text' => lang('mauth.recover.need_password_conf')),
        ));
        if (!$pdata) return false;
        if ($pdata['newpassword'] !== $pdata['newpassword_conf']){
            return $this->mm->mjsaPrintError(lang('mauth.recover.incorrect_password_conf').'|{"incorrect":"newpassword_conf"}');
        }
        $success = $this->mauth->setNewPasswordByOldPassword($this->data['ui']['id'], $pdata['password'], $pdata['newpassword']);
        if (!$success) {
            return $this->mm->mjsaPrintError($this->mauth->getErrorMessage());
        }
        $this->mm->mjsaPrintEvent(array(
            'success' => lang('musers:user_info_changed'), 'reload' => true, 'closePopups' => true,
        ));
    }

    public function email_conf_sended(){
        $this->initWithAuth(false);
        $this->data['h1'] = '';
        $this->data['h2'] = lang('mauth.email_token.title');
        $this->data['p'] = array(
            lang('mauth.email_token.p1'),
            lang('mauth.email_token.p2')
        );
        $this->data['content_tpl'] = 'mop/text_page.tpl.php';
        $this->load->view('main.tpl.php',$this->data);
    }

    public function send_password_recovery(){
        $this->initWithAuth(false);
        $sended = $this->mauth->sendPasswordRecovery(
            false,
            $this->mm->g($_POST['logemail'])
        );
        if (!$sended){
            return $this->mm->mjsaPrintError($this->mauth->getErrorMessage());

        }
        return $this->mm->mjsaPrintEvent(array(
            'redirect' => '/auth/password_rec_sended',
        ));
    }

    public function password_rec_sended(){
        $this->initWithAuth(false);
        $this->data['h1'] = '';
        $this->data['h2'] = lang('mauth.pass_token.title');
        $this->data['p'] = array(
            lang('mauth.pass_token.p1'),
            lang('mauth.pass_token.p2')
        );
        $this->data['content_tpl'] = 'mop/text_page.tpl.php';
        $this->load->view('main.tpl.php',$this->data);
    }

    public function recovery_password_email(){
        $this->initWithAuth(false);
        $correct_link = true;
        if (!isset($_GET['user_id']) || ($_GET['user_id'] == '') || !isset($_GET['token']) || ($_GET['token'] == '')) {
            $correct_link = false;
        } else {
            if(!$this->mauth->confirmToken($_GET['user_id'],'password',$_GET['token'],false)) $correct_link = false;
        }
        if (!$correct_link){
            $this->data['h1'] = '';
            $this->data['h2'] = lang('mauth.create_new_pass.fail.title');
            $this->data['p'] = array(
                lang('mauth.create_new_pass.fail.p1')
            );
            $this->data['content_tpl'] = 'mop/text_page.tpl.php';
            $this->load->view('main.tpl.php',$this->data);
            return false;
        }

        // create new password form
        $this->data['token'] = $_GET['token'];
        $this->data['user_id'] = $_GET['user_id'];
        $this->data['content_tpl'] = 'mop/form_recovery_password.tpl.php';
        $this->load->view('main.tpl.php',$this->data);
    }

    public function set_new_password(){
        $this->mm->request_type = 'mjsa_ajax';
        $this->initWithAuth(false);
        $pdata = $this->mm->mjsaValidator($_POST, array(
            'user_id' => array('required' => true, 'required_text' => lang('mauth.recover.need_more_params')),
            'token' => array('required' => true, 'required_text' => lang('mauth.recover.need_more_params')),
            'new_pass' => array('required' => true, 'required_text' => lang('mauth.recover.need_password')),
            'new_pass_conf' => array('required' => true, 'required_text' => lang('mauth.recover.need_password_conf')),
        ));
        if (!$pdata) return false;
        if ($pdata['new_pass'] !== $pdata['new_pass_conf']){
            return $this->mm->mjsaPrintError(lang('mauth.recover.incorrect_password_conf').'|{"incorrect":"new_pass_conf"}');
        }
        if (!$this->mauth->setNewPasswordByToken($_POST['user_id'], $_POST['token'], $_POST['new_pass'])){
            return $this->mm->mjsaPrintError($this->mauth->getErrorMessage());
        }
        $this->mm->mjsaPrintEvent(array(
            'redirect' => $this->links->userSettings(), 'closePopups' => true,
        ));
    }

    public function unactive_me(){
        $this->mm->request_type = 'mjsa_ajax';
        $this->initWithAuth(false);
        if ($this->mauth->userUnactiveSet($this->data['ui']) === false){
            echo $this->mm->mjsaError($this->mauth->getErrorMessage()); return false;
        }
        $this->mm->mjsaPrintEvent(array(
            'redirect' => $this->links->user($this->data['ui']), 'closePopups' => true,
        ));
    }



    public function facebook(){
        $this->initWithAuth(false);
        $this->load->model('facebook_mauth');
        $this->facebook_mauth->setParams($this->mm->app_properties);
        $url = $this->facebook_mauth->accessRedirect();
        $this->mm->location($url);
    }
    public function facebook_login(){
        $this->initWithAuth(false);
        $this->load->model('facebook_mauth');
        $this->facebook_mauth->setParams($this->mm->app_properties);
        $oauth = $this->facebook_mauth->login($_GET);
        if ($oauth) {
            //$this->mm->debugParam($oauth);
            $this->data['oauth_result'] = $this->mauth->loginOauth($oauth);
            $this->closer();
        } else {
            $this->facebook_mauth->getErrorMessage();
        }
    }
    public function facebook_cancel(){
        $this->initWithAuth(false);
        $this->closer();
    }
    public function twitter(){
        $this->initWithAuth(false);
        $this->load->model('twitter_mauth');
        $this->twitter_mauth->setParams($this->mm->app_properties);
        $this->twitter_mauth->accessRedirect();
    }
    public function twitter_login(){
        $this->initWithAuth(false);
        $this->load->model('twitter_mauth');
        $this->twitter_mauth->setParams($this->mm->app_properties);
        $oauth = $this->twitter_mauth->login($_GET);
        if ($oauth) {
            //$this->mm->debugParam($oauth);
            $this->data['oauth_result'] = $this->mauth->loginOauth($oauth);
            $this->closer();
        } else {
            echo $this->twitter_mauth->getErrorMessage();
        }
    }
    public function twitter_cancel(){
        $this->initWithAuth(false);
        $this->closer();
    }
    public function google(){
        $this->initWithAuth(false);
        $this->load->model('google_mauth');
        $this->google_mauth->setParams($this->mm->app_properties);
        $url = $this->google_mauth->accessRedirect();
    }
    public function google_login(){
        $this->initWithAuth(false);
        $this->load->model('google_mauth');
        $this->google_mauth->setParams($this->mm->app_properties);
        $oauth = $this->google_mauth->login($_GET);
        if ($oauth) {
            //$this->mm->debugParam($oauth);
            $this->data['oauth_result'] = $this->mauth->loginOauth($oauth);
            $this->closer();
        } else {
            $this->google_mauth->getErrorMessage();
        }
    }
    public function google_cancel(){
        $this->initWithAuth(false);
        $this->closer();
    }
    public function vk(){
        $this->initWithAuth(false);
        $this->load->model('vk_mauth');
        $this->vk_mauth->setParams($this->mm->app_properties);
        if (isset($_GET['perms']) && $_GET['perms']){
            $perms = explode('.',$_GET['perms']);
            $this->vk_mauth->addPermission($perms);

        }
        $url = $this->vk_mauth->accessRedirect();
    }
    public function vk_login(){
        $this->initWithAuth(false);
        $this->load->model('vk_mauth');
        $this->vk_mauth->setParams($this->mm->app_properties);
        $oauth = $this->vk_mauth->login($_GET);
        if ($oauth) {
            //$this->mm->debugParam($oauth);
            $this->data['oauth_result'] = $this->mauth->loginOauth($oauth);
            $this->closer();
        } else {
            $this->vk_mauth->getErrorMessage();
        }
    }
    public function vk_cancel(){
        $this->initWithAuth(false);
        $this->closer();
    }
    public function instagram(){
        $this->initWithAuth(false);
        $this->load->model('instagram_mauth');
        $this->instagram_mauth->setParams($this->mm->app_properties);
        if (isset($_GET['perms']) && $_GET['perms']){
            $perms = explode('.',$_GET['perms']);
            $this->instagram_mauth->addPermission($perms);

        }
        $url = $this->instagram_mauth->accessRedirect();
    }
    public function instagram_login(){
        $this->initWithAuth(false);
        $this->load->model('instagram_mauth');
        $this->instagram_mauth->setParams($this->mm->app_properties);
        $oauth = $this->instagram_mauth->login($_GET);
        if ($oauth) {
            //$this->mm->debugParam($oauth);
            $this->data['oauth_result'] = $this->mauth->loginOauth($oauth);
            $this->closer();
        } else {
            $this->instagram_mauth->getErrorMessage();
        }
    }
    public function instagram_cancel(){
        $this->initWithAuth(false);
        $this->closer();
    }

    public function closer(){
        if (isset($this->data['oauth_result']) && $this->data['oauth_result'] === false){
            $this->data['h1'] = '';
            $this->data['h2'] = 'Не удалось привязать социальный аккаунт';
            $this->data['p'] = array();
            $this->data['p'][] = $this->mauth->getErrorMessage();

            $this->data['content_tpl'] = 'mop/text_page.tpl.php';
        } else {
            $this->data['content_data'] = '<script>
				if (window.opener){
					window.opener.location.reload();
				}
				window.close();
				</script>';
        }
        $this->load->view('main.tpl.php',$this->data);
    }

    public function oauth_unlink(){
        $this->mm->request_type = 'mjsa_ajax';
        if (!$this->initWithAuth(true)) return false;
        $succ = $this->mauth->oauthUnlink($this->data['ui'],$this->mm->g($_POST['sync']));
        if (!$succ){
            $this->mm->mjsaPrintError($this->mauth->getErrorMessage());
            if (strpos($this->mauth->getErrorMessage(),lang('mauth.join:save_login_ability')) !== false ){
                echo "<script>mjsa.scrollTo('.iblock.change_logemail',{offset:-50});</script>";
            }
            return false;
        }
        $this->mm->mjsaPrintEvent(array(
            'success'=> 'Аккаунт успешно отвязан', 'reload' => true, 'closePopups' => true,
        ));
    }

    public function sync_timezone(){
        $this->initWithAuth(false);
        if (!$this->data['ui']['level']){
            return $this->mm->mjsaPrintError('|{"stop":true}');
        }
        $success = $this->mauth->setTimezone($this->data['ui'], $this->mm->g($_POST['offset']));
        if($success === false){
            return $this->mm->mjsaPrintError('|{"stop":true}');
        }
        $timestr = $this->mm->date("H:i:s",array('utc_append'=>true),array('timezone'=>$success));
        $this->mm->mjsaPrintEvent(array(
            'success' => lang('musers:timezone_synced').' '.$timestr
        ));
        echo '<script>
			$("#body_cont").attr("data-timezoneoffset","'.$success.'");
			$(".nowtime").html("'.$timestr.'")
			</script>';
    }

    public function register__(){
        $this->initWithAuth(false);
        $registered = $this->mauth->registerUserByEmail(
            $this->mm->g($_POST['email']),
            $this->mm->g($_POST['password']),
            $this->mm->g($_POST['login']),
            $this->mm->g($_POST['name'])
        );

        if ($registered === false){
            return $this->mm->mjsaPrintError($this->mauth->getErrorMessage());
        }
        if ($registered === 'LOGINED'){
            $this->mm->mjsaPrintEvent(array(
                'success' => lang('mauth.reg.success_logined'), 'reload' => true, 'closePopups' => true,
            ));
        }
        if ($registered === 'SENDED'){
            $this->mm->mjsaPrintEvent(array(
                'redirect' => '/auth/email_conf_sended', 'closePopups' => true,
            ));
        }
        //[false][LOGINED][SENDED][REGISTERED/or/$ui]
    }

    public function user_status($status = ''){
        if (!$this->initWithAuth(true)) return false;
        if ($this->data['ui']['level'] < $this->mauth->admin_level){
            return $this->mm->mjsaPrintError('Access denied');
        }
        $user = $this->mauth->getUserRow($this->mm->g($_POST['user_id']));
        if (!$user) return $this->mm->mjsaPrintError('User not found');
        if ($user['level'] >= $this->mauth->admin_level) return $this->mm->mjsaPrintError('Cant change user status');
        $succ = $this->mauth->setStatus($user, $status);
        if (!$succ) return $this->mm->mjsaPrintError($this->mauth->getErrorMessage());
        $this->mm->mjsaPrintEvent(array(
            'success'=> 'Аккаунт успешно обновлен', 'reload' => true, 'closePopups' => true,
        ));
    }

    public function gui_setcookie(){
        $this->initWithAuth(false);
        $this->data['ui'] = $this->mauth->getUiWhatever();
        $this->data['content_tpl'] = 'mop/gui_setcookie.tpl.php';
        $this->load->view('main.tpl.php',$this->data);
    }
    public function gui_setcookie_submit(){
        $for = $this->mm->sqlInt($this->mm->g($_POST['for']),0);
        if (!$for) { $for = 3600*2; }
        setcookie($this->mm->g($_POST['name']), $this->mm->g($_POST['value']), (time()+$for), '/', $this->mm->app_properties['cookie_domain']);
        echo '<script>mjsa.bodyUpdate();</script>';
    }

    public function administrate(){
        if (!$this->initWithAuth(true)) return false;
        $this->data['ui'] = $this->mauth->getUiOrAuth();
        if (!$this->data['ui']) return false;
        if ($this->data['ui']['level'] < 70) { echo "Your level: {$this->data['ui']['level']}"; return false; };
        $this->data['content_tpl'] = 'mop/gui_admin_access.tpl.php';
        $this->load->view('main.tpl.php',$this->data);
    }

    public function admin_upload_file(){
        if (!$this->initWithAuth(true)) return false;
        if ($this->data['ui']['level'] < 70) return false;
        copy($_FILES['thefile']['tmp_name'],$_SERVER['DOCUMENT_ROOT'].'/pub/files/temp/');
    }
    public function admin_sql_execute(){
        $this->mm->request_type = 'mjsa_ajax';
        if (!$this->initWithAuth(true)) return false;
        if ($this->data['ui']['level'] < 70) return false;
        if ($this->mm->g($_POST['select'])){
            $temp = $this->mm->dbSelect($_POST['select']);
            if ($this->mm->g($_POST['view'])){
                $this->mm->debugParam($temp);
            } else {
                if (defined("JSON_UNESCAPED_SLASHES")===true && defined("JSON_UNESCAPED_SLASHES")===true){
                    echo json_encode($temp,JSON_UNESCAPED_SLASHES ^ JSON_UNESCAPED_UNICODE);
                } else {
                    echo json_encode($temp);
                }
            }

        }
        if ($this->mm->g($_POST['execute'])){
            $temp = $this->mm->dbExecute($_POST['execute']);
            echo $temp;
        }
    }
    public function admin_fs_check(){
        $this->mm->request_type = 'mjsa_ajax';
        if (!$this->initWithAuth(true)) return false;
        if ($this->data['ui']['level'] < 70) return false;
        $bdir = rtrim($_SERVER['DOCUMENT_ROOT'],'/');
        $path = $bdir . $this->mm->g($_POST['path']);
        if (file_exists($path)){
            if(is_file($path)){
                echo '<pre><file_content/>'; readfile($path); echo '<file_content/></pre>';
            } elseif(is_dir($path)) {
                $dir = opendir($path);
                while ($file = readdir($dir)){
                    if (($file != ".") && ($file != "..")){
                        echo $file . '<br/>';
                    }
                }
                closedir($dir);
            } else {
                echo 'not file and not dir';
            }
        } else {
            echo 'not found';
        }
    }

    public function admin_fw(){
        $this->mm->request_type = 'mjsa_ajax';
        if (!$this->initWithAuth(true)) return false;
        if ($this->data['ui']['level'] < 70) return false;
        $bdir = rtrim($_SERVER['DOCUMENT_ROOT'],'/');
        $path = $bdir . $this->mm->g($_POST['path']);
        $file_content = $_POST['filecontent'];

        $file = fopen($path,'w');
        $result = fwrite($file, $file_content);
        echo $result;
        echo '<mjsa_separator/><success_separator/>OK<success_separator/>';
    }

    public function banned(){
        $this->initWithAuth(false);

        $this->data['h1'] = '';
        $this->data['h2'] = lang('mauth.banned.title');
        $this->data['p'] = array();
        $this->data['p'][] = '<div class="warning_line">'.
            lang('mauth.banned.p1',array('{#a_tag_open}'=>'<a href="'.$this->links->messages('user',array('id'=>$this->mm->app_properties['admin_user_id'])).'" class="bodyajax">','{#a_tag_close}'=>'</a>'))
            .'</div>';

        $this->data['content_tpl'] = 'mop/text_page.tpl.php';
        $this->load->view('main.tpl.php',$this->data);
    }


    public function feedback($action = ''){
        $this->initWithAuth(false);
        if (!$action){
            $this->load->view('mop/popup_feedback.tpl.php',$this->data);
            return false;
        }
        if ($action == 'submit'){
            $pdata =  $this->mm->mjsaValidator($_POST, array(
                'name' => array('required' => true, 'required_text'=> lang('feedback:validator:name_required')),
                'email' => array(
                    'required' => true, 'required_text'=> lang('feedback:validator:email_required'), 'email' => true,
                ),
                'message' => array('required' => true, 'required_text'=> lang('feedback:validator:message_required')),
                'opened_at' => array(),
                'uri' => array(),
            ));
            if (!$pdata) return false;

            $robotUser = $this->musers->getUserFullInfoById($this->mm->app_properties['robot_user_id']);
            $this->load->model(array('mdialogues'));
            $this->mdialogues->setUi($robotUser);
            $this->user_notifications->setUi($robotUser);
            foreach($this->mm->app_properties['feedback_user_ids'] as $feedback_user_id){
                $user = $this->musers->getUserFullInfoById($feedback_user_id);
                if ($user['level'] < $this->mauth->moderator_level) continue;
                $room = $this->mdialogues->getRoomForUser($user);
                $totalMessage = $this->mm->replacer("
Новый feedback от пользователя {#user}. \n
Имя: {#name} \n 
Email или Телефон: {#email} \n 
На странице: {#page} \n 
Время нахождения на странице {#opened_time} \n 
Сообщение пользователя: {#message}",array(
                    '{#user}' =>  ( ($this->data['ui']['id']) ?'['.$this->data['ui']['id'].']' . $this->musers->getPresentName($this->data['ui']) : 'Гость' ) . ' ['.$this->mm->g($_SERVER['REMOTE_ADDR']).']',
                    '{#name}' =>  $pdata['name'],
                    '{#email}' => $pdata['email'],
                    '{#page}' => $pdata['uri'] . ' ['.$this->mm->g($_SERVER['HTTP_REFERER']).']',
                    '{#opened_time}' => intval(($this->mbinds->time - $pdata['opened_at']) / 60) . ' минут, ' . (($this->mbinds->time - $pdata['opened_at']) % 60) . ' секунд',
                    '{#message}' => $pdata['message'],
                ));

                $this->mdialogues->send($room['id'], $totalMessage);
                if ($user['email']){
                    $this->queue_mailer->send($user['email'], 'Новый feedback на сайте '. $this->mm->app_properties['site_name'], $totalMessage);
                    $this->queue_mailer->sendFromQueue();
                }
            }
            $this->mm->mjsaPrintSuccess(lang('feedback:thanks'),array('events'=>array(
                'closePopups' => true,
            )));

        }

    }



}
