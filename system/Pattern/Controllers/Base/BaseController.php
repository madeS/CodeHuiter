<?php

namespace CodeHuiter\Pattern\Controllers\Base;

use CodeHuiter\Config\Config;
use CodeHuiter\Core\Application;
use CodeHuiter\Core\Controller;
use CodeHuiter\Core\Exceptions\ExceptionProcessor;
use CodeHuiter\Exceptions\CodeHuiterException;
use CodeHuiter\Exceptions\ErrorException;
use CodeHuiter\Pattern\Modules\Auth\AuthService;
use CodeHuiter\Pattern\Modules\Auth\Models\UsersModel;
use CodeHuiter\Services\Compressor;

/**
 * The base pattern controller
 *
 * @property-read Compressor $compressor
 * @property-read \App\Services\Links $links
 * @property-read \CodeHuiter\Pattern\Modules\Auth\AuthService $auth
 * @property-read \CodeHuiter\Services\Mjsa $mjsa
 */
class BaseController extends Controller
{
    /** @var array $config config[Config::CONFIG_KEY_MAIN] in configs */
    public $config;

    /** @var array $data */
    protected $data;

    /** @var array $runData */
    public $runData;

    public function __construct(Application $app)
    {
        parent::__construct($app);
        $this->config = $this->app->getConfig(Config::CONFIG_KEY_MAIN);

        $this->init();
    }

    protected function error404()
    {
        try {
            $this->log->warning('Page 404 showed with uri ['.$this->request->uri.']', [], 'exceptions');

            $this->router->setRouting('error_404', []);
            $this->router->execute();
        } catch (CodeHuiterException $exception) {
            $this->error500('', $exception);
        }
    }

    /**
     * @param string $message
     * @param \Exception | null $exception
     */
    protected function error500($message = 'Custom 500 error exception', $exception = null)
    {
        try {
            $this->log->error($exception->getMessage(), ['exception' => $exception], 'exceptions');

            if ($exception === null) {
                $exception = new ErrorException($message);
            }
            $this->router->setRouting('error_500', [$exception]);
            $this->router->execute();
        } catch (CodeHuiterException $exceptionInner) {
            // Use default framework exception (FATAL)
            ExceptionProcessor::defaultProcessException($exceptionInner);
        }
    }

    protected function render($subTemplate, $return = false)
    {
        if (!isset($this->data['userInfo'])) {
            $this->data['userInfo'] = $this->auth->getDefaultUser();
        }

        $this->benchmark->mark('RenderStart');
        $this->data['template'] = ':' . $this->config['template'];
        $this->data['content_tpl'] = $subTemplate;
        $this->response->render($this->data['template'] . '/main', $this->data, $return);
    }

    protected function initWithAuth(
        $require = false,
        $requiredGroups = [
            AuthService::GROUP_NOT_BANNED,
            AuthService::GROUP_ACTIVE,
        ],
        $customActions = []
    ) {
        $those = $this;
        $success = $this->auth->initUser($require, $requiredGroups , ([
            AuthService::GROUP_AUTH_SUCCESS => function(/** @noinspection PhpUnusedParameterInspection */UsersModel $user) use ($those) {
                // User Not authed
                if ($those->request->isMjsaAJAX()) {
                    $this->data['in_popup'] = true;
                    $this->mjsa->openPopupWithData(
                        $this->response->render($this->auth->getViewsPath() . 'login', $this->data, true),
                        'authPopup',
                        ['maxWidth' => 600, 'close' => true,]
                    );
                } else {
                    $addUrl = ($those->request->uri) ? '?url=' . urlencode($those->request->uri) : '';
                    $those->response->location($those->auth->config['url_auth'] . $addUrl, true);
                }
            },
            AuthService::GROUP_NOT_BANNED => function(/** @noinspection PhpUnusedParameterInspection */UsersModel $user) use ($those) {
                // User banned
                if ($those->request->isMjsaAJAX()) {
                    $this->mjsa->events()
                        ->errorMessage($this->lang->get('auth:user_banned'))
                        ->closePopups()
                        ->send();
                } else {
                    $those->response->location($those->auth->config['url_ban'], true);
                }
            },
            AuthService::GROUP_ACTIVE => function(/** @noinspection PhpUnusedParameterInspection */UsersModel $user) use ($those) {
                // User banned
                if ($those->request->isMjsaAJAX()) {
                    $this->mjsa->events()
                        ->errorMessage($this->lang->get('auth:user_not_active'))
                        ->closePopups()
                        ->send();
                } else {
                    $those->response->location($those->auth->config['url_active'], true);
                }
            },
        ] + $customActions));

        $this->data['ui'] = ($success) ? $this->auth->user : false;
        return $success;
    }

    protected function init()
    {
        $this->runData = [
            'bodyAjax' => $this->request->isBodyAJAX(),
            'language' => $this->config['language'],
        ];
        $this->lang->setLanguage($this->runData['language']);
        $this->data = [];
        foreach($this->config['data_default'] as $defaultField) {
            $this->data[$defaultField] = $this->config[$defaultField];
        }
    }
}
