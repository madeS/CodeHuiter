<?php

namespace CodeHuiter\Pattern\Controllers\Base;

use CodeHuiter\Config\Config;
use CodeHuiter\Core\Application;
use CodeHuiter\Core\Controller;
use CodeHuiter\Core\Exceptions\ExceptionProcessor;
use CodeHuiter\Exceptions\CodeHuiterException;
use CodeHuiter\Exceptions\ErrorException;
use CodeHuiter\Pattern\Modules\Auth\AuthService;
use CodeHuiter\Pattern\Modules\Auth\Models\UserInterface;
use CodeHuiter\Services\Compressor;

/**
 * The base pattern controller
 *
 * @property-read Compressor $compressor
 * @property-read \App\Services\Links $links
 * @property-read \CodeHuiter\Pattern\Services\Media $media
 * @property-read \CodeHuiter\Pattern\Modules\Auth\AuthService $auth
 * @property-read \CodeHuiter\Services\Mjsa $mjsa
 */
class BaseController extends Controller
{
    /** @var array $data */
    protected $data;

    /** @var array $runData */
    public $runData;

    public function __construct(Application $app)
    {
        parent::__construct($app);

        $this->init();
    }

    protected function errorPageByCode($code = 404, $message)
    {
        try {
            $this->log->warning('Page '. $code .' showed with uri ['.$this->request->uri.']', [], 'exceptions');

            $this->router->setRouting('error' . $code, [$message]);
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
            $this->router->setRouting('error500', [$exception]);
            $this->router->execute();
        } catch (CodeHuiterException $exceptionInner) {
            // Use default framework exception (FATAL)
            ExceptionProcessor::defaultProcessException($exceptionInner);
        }
    }

    protected function render($contentTpl, $return = false)
    {
        if (!isset($this->data['userInfo'])) {
            $this->data['userInfo'] = $this->auth->getDefaultUser();
        }

        $this->benchmark->mark('RenderStart');
        $this->data['patternTemplate'] = SYSTEM_PATH . 'Pattern/Views/';
        $this->data['template'] = VIEW_PATH . $this->app->config->projectConfig->template;
        $this->data['headAfterTpl'] = $this->app->config->projectConfig->headAfterTpl;
        $this->data['bodyAfterTpl'] = $this->app->config->projectConfig->bodyAfterTpl;
        $this->data['contentTpl'] = $contentTpl;

        $this->response->render($this->data['patternTemplate'] . '/main', $this->data, $return);
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
            AuthService::GROUP_AUTH_SUCCESS => function(/** @noinspection PhpUnusedParameterInspection */UserInterface $user) use ($those) {
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
                    $those->response->location($those->auth->config->urlAuth . $addUrl, true);
                }
            },
            AuthService::GROUP_NOT_BANNED => function(/** @noinspection PhpUnusedParameterInspection */UserInterface $user) use ($those) {
                // User banned
                if ($those->request->isMjsaAJAX()) {
                    $this->mjsa->events()
                        ->errorMessage($this->lang->get('auth:user_banned'))
                        ->closePopups()
                        ->send();
                } else {
                    $those->response->location($those->auth->config->urlBan, true);
                }
            },
            AuthService::GROUP_ACTIVE => function(/** @noinspection PhpUnusedParameterInspection */UserInterface $user) use ($those) {
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

        $this->data['userInfo'] = ($success) ? $this->auth->user : false;
        return $success;
    }

    protected function init()
    {
        $this->lang->setLanguage($this->app->config->settingsConfig->language);
        $this->data = [
            'bodyAjax' => $this->request->isBodyAJAX(),
            'language' => $this->app->config->settingsConfig->language,
            'siteUrl' => $this->app->config->settingsConfig->siteUrl,
        ];
        foreach($this->app->config->projectConfig->dataDefault as $defaultField) {
            $this->data[$defaultField] = $this->app->config->projectConfig->$defaultField;
        }

        $this->lang->setLanguage($this->data['language']);
    }
}
