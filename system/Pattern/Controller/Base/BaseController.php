<?php

namespace CodeHuiter\Pattern\Controller\Base;

use CodeHuiter\Config\PatternConfig;
use CodeHuiter\Core\Application;
use CodeHuiter\Core\Controller;
use CodeHuiter\Core\Exception\ExceptionProcessor;
use CodeHuiter\Exception\CodeHuiterException;
use CodeHuiter\Exception\ErrorException;
use CodeHuiter\Pattern\Module\Auth\AuthService;
use CodeHuiter\Pattern\Module\Auth\Model\UserInterface;
use CodeHuiter\Pattern\Service\Compressor;

/**
 * The base pattern controller
 *
 * @property-read Compressor $compressor
 * @see PatternConfig::SERVICE_KEY_COMPRESSOR There are Forward Usages
 *
 * @property-read \App\Service\Link $links
 * @see PatternConfig::SERVICE_KEY_LINKS There are Forward Usages
 *
 * @property-read \CodeHuiter\Pattern\Service\Media $media
 * @see PatternConfig::SERVICE_KEY_MEDIA There are Forward Usages
 *
 * @property-read \CodeHuiter\Pattern\Module\Auth\AuthService $auth
 * @see PatternConfig::SERVICE_KEY_AUTH There are Forward Usages
 *
 * @property-read \CodeHuiter\Pattern\Service\MjsaResponse $mjsaResponse
 * @see PatternConfig::SERVICE_KEY_MJSA_RESPONSE There are Forward Usages
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

    protected function errorPageByCode($code = 404, $message = '')
    {
        try {
            $this->log->withTag('exceptions')->warning('Page '. $code .' showed with uri ['.$this->request->uri.']', []);

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
            $this->log->withTag('exceptions')->error($exception->getMessage(), ['exception' => $exception]);

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

        $this->loader->benchmarkPoint('RenderStart');
        $this->data['patternTemplate'] = SYSTEM_PATH . 'Pattern/View/';
        $this->data['template'] = VIEW_PATH . $this->app->config->projectConfig->template;
        $this->data['headAfterTpl'] = $this->app->config->projectConfig->headAfterTpl;
        $this->data['bodyAfterTpl'] = $this->app->config->projectConfig->bodyAfterTpl;
        $this->data['contentTpl'] = $contentTpl;

        $this->renderer->render($this->data['patternTemplate'] . '/main', $this->data, $return);
    }

    protected function initWithAuth(
        $require,
        $requiredGroups = [
            AuthService::GROUP_NOT_BANNED,
            AuthService::GROUP_ACTIVE,
        ],
        $customActions = []
    ) {
        $those = $this;
        $success = $this->auth->initUser($require, $requiredGroups , [
            AuthService::GROUP_AUTH_SUCCESS => function(/** @noinspection PhpUnusedParameterInspection */UserInterface $user) use ($those) {
                // User Not authed
                if ($this->mjsaResponse->isMjsaRequested($this->request)) {
                    $this->data['in_popup'] = true;
                    $this->mjsaResponse->openPopupWithData(
                        $this->renderer->render($this->auth->getViewsPath() . 'login', $this->data, true),
                        'authPopup',
                        ['maxWidth' => 600, 'close' => true,]
                    )->render($this->response);
                } else {
                    $addUrl = ($those->request->uri) ? '?url=' . urlencode($those->request->uri) : '';
                    $those->response->location($those->auth->config->urlAuth . $addUrl, true);
                }
            },
            AuthService::GROUP_NOT_BANNED => function(/** @noinspection PhpUnusedParameterInspection */UserInterface $user) use ($those) {
                // User banned
                if ($this->mjsaResponse->isMjsaRequested($this->request)) {
                    $this->mjsaResponse
                        ->errorMessage($this->lang->get('auth:user_banned'))
                        ->closePopups()
                        ->render($this->response);
                } else {
                    /** TODO Implement this page */
                    $those->response->location($those->auth->config->urlBan, true);
                }
            },
            AuthService::GROUP_ACTIVE => function(/** @noinspection PhpUnusedParameterInspection */UserInterface $user) use ($those) {
                // User banned
                if ($this->mjsaResponse->isMjsaRequested($this->request)) {
                    $this->mjsaResponse
                        ->errorMessage($this->lang->get('auth:user_not_active'))
                        ->closePopups()
                        ->render($this->response);
                } else {
                    /** TODO Implement this page */
                    $those->response->location($those->auth->config->urlActive, true);
                }
            },
        ] + $customActions);

        $this->data['userInfo'] = ($success) ? $this->auth->user : $this->auth->getDefaultUser();
        return $success;
    }

    protected function init()
    {
        $this->lang->setLanguage($this->app->config->settingsConfig->language);
        $this->data = [
            'bodyAjax' => $this->mjsaResponse->isBodyAjax($this->request),
            'language' => $this->app->config->settingsConfig->language,
            'siteUrl' => $this->app->config->settingsConfig->siteUrl,
        ];
        foreach($this->app->config->projectConfig->dataDefault as $defaultField) {
            $this->data[$defaultField] = $this->app->config->projectConfig->$defaultField;
        }

        $this->lang->setLanguage($this->data['language']);
    }
}
