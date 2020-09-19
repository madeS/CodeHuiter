<?php

namespace CodeHuiter\Facilities\Module\Auth\Controller;

use CodeHuiter\Core\Response;
use CodeHuiter\Facilities\Controller\Base\BaseController;
use CodeHuiter\Facilities\Module\Auth\AuthService;
use CodeHuiter\Facilities\Module\Auth\Oauth\OAuthFactory;

class AuthController extends BaseController
{
    /**
     * @return bool|void
     */
    public function index(): void
    {
        $this->initWithAuth(false);
        if ($this->auth->user->exist()) {
            $redirectUrl = '/';
            $userRedirectUrl = $this->request->getGet('url');
            if ($userRedirectUrl && strpos($userRedirectUrl, '/') === 0) {
                $redirectUrl = $userRedirectUrl;
            }

            if ($this->ajaxResponse->isAjaxRequested($this->request)) {
                $this->ajaxResponse->location($redirectUrl);
            } else {
                $this->response->location($redirectUrl, true);
            }

            return;
        }

        if ($this->ajaxResponse->isAjaxRequested($this->request) && $this->request->getGet('in_popup')) {
            $this->data['in_popup'] = true;
            $this->ajaxResponse->openPopupWithData(
                $this->renderer->render($this->app->config->authConfig->viewsPath . 'login', $this->data, true),
                'authPopup',
                ['maxWidth' => 600, 'close' => true,]
            )->render($this->response);
        } else {
            $this->render($this->app->config->authConfig->viewsPath . 'login');
        }
    }

    public function register_submit(): void
    {
        $this->initWithAuth(false);
        $targetUi = $this->auth->user->exist() ? $this->auth->user : null;

        $data = $this->auth->registerByEmailValidator($this->validator, $this->ajaxResponse, $this->request->getPostAsArray(), [], $targetUi);
        if (!$data) {
            $this->ajaxResponse->render($this->response);
            return;
        }
        $result = $this->auth->registerByEmail($data->getField('email'), $data->getField('password'), $data->getField('login'), $targetUi);
        if ($result->isSuccess()) {
            //$this->mjsa->successMessage($result->getMessage());
            $this->ajaxResponse->closePopups()->reload()->render($this->response);
        } elseif ($result->isSpecific() && isset($result->getFields()['confirmation'])) {
            $this->ajaxResponse->formReplace($this->renderer->render(
                $this->app->config->authConfig->viewsPath . 'formMessage',
                ['message' => $result->getMessage(), 'messageType' => 'success'],
                true
            ))->render($this->response);
        } else {
            if ($result->isIncorrectField()) {
                foreach ($result->getFields() as $field) {
                    $this->ajaxResponse->incorrect($field);
                }
            }
            $this->ajaxResponse->errorMessage($result->getMessage())->render($this->response);
        }
    }

    public function login_submit(): void
    {
        $this->initWithAuth(false);
        $data = $this->auth->loginByPasswordValidator($this->validator, $this->ajaxResponse, $this->request->getPostAsArray());
        if (!$data) {
            $this->ajaxResponse->render($this->response);
            return;
        }
        $result = $this->auth->loginByPassword($data->getField('logemail'), $data->getField('password'));
        if ($result->isSuccess()) {
            $this->ajaxResponse->closePopups();
            $continueUri = $this->request->getPost('continue_url');
            if ($continueUri && $continueUri[0] === '/') {
                $this->ajaxResponse->location($continueUri);
            } else {
                $this->ajaxResponse->reload();
            }
            $this->ajaxResponse->render($this->response);
        } elseif ($result->isSpecific() && isset($result->getFields()['confirmation'])) {
            $this->ajaxResponse->formReplace($this->renderer->render(
                $this->app->config->authConfig->viewsPath . 'formMessage',
                ['message' => $result->getMessage(), 'messageType' => 'success'],
                true
            ))->render($this->response);
        } else {
            if ($result->isIncorrectField()) {
                foreach ($result->getFields() as $field) {
                    $this->ajaxResponse->incorrect($field);
                }
            }
            $this->ajaxResponse->errorMessage($result->getMessage())->render($this->response);
        }
    }

    public function logout(): void
    {
        /**
         * TODO only POST request allow
         */
        $this->initWithAuth(false);
        if ($this->auth->user->exist()) {
            $this->auth->resetSig($this->auth->user, true);
        }
        if ($this->ajaxResponse->isAjaxRequested($this->request)) {
            // TODO mjsa location
        } else {
            $this->response->location($_SERVER['HTTP_REFERER'] ?? $this->links->main(),true);
        }
    }

    public function confirm_email(): void
    {
        $result = $this->auth->activateEmail(
            $this->request->getGet('user_id', ''),
            $this->request->getGet('token', '')
        );
        if (!$result->isSuccess()) {
            $this->errorPageByCode(Response::HTTP_CODE_FORBIDDEN, $result->getMessage());
        } else {
            if ($this->ajaxResponse->isAjaxRequested($this->request)) {
                $this->ajaxResponse->location($this->links->userSettings())->render($this->response);
            } else {
                $this->response->location($this->links->userSettings(), true);
            }
        }
    }

    public function password_recovery_submit(): void
    {
        /**
         * TODO csrf tocken implement for all post requests
         */
        $this->initWithAuth(false);
        $result = $this->auth->sendPasswordRecoveryByLogemail(
            $this->request->getPost('logemail', '')
        );
        if ($result->isSuccess()) {
            $this->ajaxResponse->formReplace($this->renderer->render(
                $this->app->config->authConfig->viewsPath . 'formMessage',
                ['message' => $this->lang->get('auth_sign:recovery_link_sent'), 'messageType' => 'success'],
                true
            ))->render($this->response);
        } else {
            if ($result->isIncorrectField()) {
                foreach ($result->getFields() as $field) {
                    $this->ajaxResponse->incorrect($field);
                }
            }
            $this->ajaxResponse->errorMessage($result->getMessage())->render($this->response);
        }
    }

    public function recovery(): void
    {
        $this->initWithAuth(false);
        $user = $this->auth->getUserById((int)$this->request->getGet('user_id', ''));
        if (!$user) {
            $this->errorPageByCode(Response::HTTP_CODE_FORBIDDEN, $this->lang->get('auth_sign:incorrect_id'));
            return;
        }
        $token = $this->request->getGet('token', '');
        $this->data['user'] = $user;
        $this->data['email_token'] = $token;
        if (!$this->auth->isValidToken($user, $token, AuthService::TOKEN_TYPE_RECOVERY)) {
            $this->errorPageByCode(Response::HTTP_CODE_FORBIDDEN, $this->lang->get('auth_sign:incorrect_token'));
            return;
        }
        $this->render($this->app->config->authConfig->viewsPath . 'passwordChangeTemplate');
    }

    public function user_edit_password_submit(): void
    {
        $this->initWithAuth(false);
        $token = $this->request->getPost('token', '');
        if ($token) {
            // By Token
            $validatorConfig = [
                'token' => ['required' => true, 'required_text' => $this->lang->get('auth_sign:recovery_need_old_password')],
                'user_id' => ['required' => true, 'required_text' => $this->lang->get('auth_sign:recovery_need_user_id')],
                'newpassword' => array('required' => true, 'required_text' => $this->lang->get('auth_sign:need_password')),
                'newpassword_conf' => array('required' => true, 'required_text' => $this->lang->get('auth_sign:need_password_conf')),
            ];
            $pdata = $this->validator->validate($this->request->getPostAsArray(), $validatorConfig, $this->ajaxResponse);
            if (!$pdata) {
                $this->ajaxResponse->render($this->response);
                return;
            }
            if ($pdata->getField('newpassword') !== $pdata->getField('newpassword_conf')) {
                $this->ajaxResponse->incorrect('newpassword_conf')->errorMessage($this->lang->get('auth_sign:incorrect_password_conf'))->render($this->response);
                return;
            }
            $result  = $this->auth->setNewPasswordByToken((int)$pdata->getField('user_id'), $pdata->getField('token'), $pdata->getField('newpassword'));
            if (!$result->isSuccess()) {
                $this->ajaxResponse->errorMessage($result->getMessage())->render($this->response);
                return;
            }
            $this->ajaxResponse->successMessage('auth_sign:user_info_changed')->location($this->links->userSettings())->closePopups()->render($this->response);
        } else {
            // By Password
            if (!$this->auth->user->exist()) {
                $this->ajaxResponse->errorMessage($this->lang->get('auth_sign:recovery_need_to_be_sign'))->render($this->response);
            }
            $validatorConfig = [
                'password' => ['required' => true, 'required_text' => $this->lang->get('auth_sign:recovery_need_old_password')],
                'newpassword' => array('required' => true, 'required_text' => $this->lang->get('auth_sign:need_password')),
                'newpassword_conf' => array('required' => true, 'required_text' => $this->lang->get('auth_sign:need_password_conf')),
            ];
            $pdata = $this->validator->validate($this->request->getPostAsArray(), $validatorConfig, $this->ajaxResponse);
            if (!$pdata) {
                $this->ajaxResponse->render($this->response);
                return;
            }
            if ($pdata->getField('newpassword') !== $pdata->getField('newpassword_conf')) {
                $this->ajaxResponse->incorrect('newpassword_conf')->errorMessage($this->lang->get('auth_sign:incorrect_password_conf'))->render($this->response);
                return;
            }
            $result = $this->auth->setNewPasswordByOldPassword($this->auth->user->getId(), $pdata->getField('password'), $pdata->getField('newpassword'));
            if (!$result->isSuccess()) {
                $this->ajaxResponse->errorMessage($result->getMessage())->render($this->response);
                return;
            }
            $this->ajaxResponse->successMessage('auth_sign:user_info_changed')->reload()->closePopups()->render($this->response);
        }
    }

    public function user_edit_logemail_submit(): void
    {
        if (!$this->initWithAuth(true)) return;
        $data = $this->validator->validate($this->request->getPostAsArray(), [
            'email' => ['required' => true, 'required_text' => $this->lang->get('auth_sign:need_email')],
            'login' => ['required' => false],
            'password' => ['required' => true, 'required_text' => $this->lang->get('auth_sign:need_password')],
        ], $this->ajaxResponse);
        if (!$data) { $this->ajaxResponse->render($this->response); return; }
        $result = $this->auth->registerByEmail(
            $data->getField('email'),
            $data->getField('password'),
            $data->getField('login'),
            $this->auth->user
        );
        if ($result->isSuccess()) {
            $this->ajaxResponse->closePopups()->successMessage('auth_sign:user_info_changed')->reload()->render($this->response);
        } elseif ($result->isSpecific() && isset($result->getFields()['confirmation'])) {
            $this->ajaxResponse->formReplace($this->renderer->render(
                $this->app->config->authConfig->viewsPath . 'formMessage',
                ['message' => $result->getMessage(), 'messageType' => 'success'],
                true
            ))->render($this->response);
        } else {
            if ($result->isIncorrectField()) {
                foreach ($result->getFields() as $field) {
                    $this->ajaxResponse->incorrect($field);
                }
            }
            $this->ajaxResponse->errorMessage($result->getMessage())->render($this->response);
        }
    }


    private function getOAuthManagerFactory(): OAuthFactory
    {
        return new OAuthFactory(
            $this->network,
            $this->log,
            $this->app->config->webConfig,
            $this->app->config->authConfig
        );
    }

    public function oauth(string $type = ''): void
    {
        $this->initWithAuth(false);
        $manager = $this->getOAuthManagerFactory()->createOAuthManager($type);
        if (!$manager) {
            $this->errorPageByCode(Response::HTTP_CODE_NOT_FOUND);
            return;
        }
        $additionalPerms = $this->request->getGet('perms');
        if ($additionalPerms) {
            $perms = explode('.', $additionalPerms);
            $manager->addPermission($perms);
        }
        $this->response->location($manager->getSourceAccessLink(), true);
    }

    public function oauth_success(string $type = ''): void
    {
        $this->initWithAuth(false);
        $manager = $this->getOAuthManagerFactory()->createOAuthManager($type);
        if (!$manager) {
            $this->errorPageByCode(Response::HTTP_CODE_NOT_FOUND);
            return;
        }
        $oauthData = $manager->login($this->request->getGetAsArray());
        if ($oauthData === null) {
            $this->oauthCloser($manager->getLastErrorMessage());
            return;
        }
        $result = $this->auth->loginByOauth($oauthData, true);
        if ($result->isError()) {
            $this->oauthCloser($result->getMessage());
            return;
        }
        $this->oauthCloser(null);
    }
    public function oauth_cancel(){
        $this->initWithAuth(false);
        $this->oauthCloser(null);
    }
    private function oauthCloser(?string $failMessage)
    {
        if ($failMessage !== null) {
            $this->data['h1'] = '';
            $this->data['h2'] = 'Не удалось привязать социальный аккаунт';
            $this->data['p'] = [];
            $this->data['p'][] = $failMessage;
            $this->render($this->app->config->projectConfig->baseTemplatePath . '/contents/textPageTemplate');
        } else {
            $this->render($this->app->config->projectConfig->baseTemplatePath . '/contents/autoCloserTemplate');
        }
    }

    public function unactive_me(): void
    {
        if (!$this->initWithAuth(true)) return;
        $this->auth->deactivateUser($this->auth->user, true);
        $this->ajaxResponse->location($this->links->user($this->auth->user))->closePopups()->render($this->response);
    }

    public function sync_timezone(): void
    {
        $this->initWithAuth(false);
        if (!$this->auth->user->exist()) {
            return;
        }
        $timzoneOffset = $this->auth->setTimezone($this->auth->user, $this->request->getPost('offset', 0));
        if($timzoneOffset === null){
            return;
        }
        $timeStr = $this->date->fromTime()->forUser($this->auth->user)->toFormat('H:i:s',false,true);
        $this->ajaxResponse->successMessage(
            $this->lang->get('user:settings.timezone_synced', ['{#time}' => $timeStr])
        )->append(
            '#m_service',
            '<script>'
            . '$("#body_cont").attr("data-timezoneoffset","'.$timzoneOffset.'");'
            . '$(".nowtime").html("'.$timeStr.'");'
            . '</script>'
        )->render($this->response);
    }








    public function set_language($language = ''){
        setcookie('language', $language, time() + 3600*30, '/');
        $this->mm->mjsaPrintEvent(array(
            /* 'success'=> 'Язык изменен', */ 'reload' => true, 'closePopups' => true,
        ));
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
            lang('mauth.banned.p1',array('{#a_tag_open}'=>'<a href="'.$this->links->messagesWithUserById($this->mm->app_properties['admin_user_id']).'" class="bodyajax">','{#a_tag_close}'=>'</a>'))
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
