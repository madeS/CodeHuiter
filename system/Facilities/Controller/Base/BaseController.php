<?php

namespace CodeHuiter\Facilities\Controller\Base;

use CodeHuiter\Config\FacilitiesConfig;
use CodeHuiter\Core\Application;
use CodeHuiter\Core\Controller;
use CodeHuiter\Core\Exception\ExceptionProcessor;
use CodeHuiter\Exception\CodeHuiterException;
use CodeHuiter\Exception\ErrorException;
use CodeHuiter\Facilities\Module\Auth\AuthService;
use CodeHuiter\Facilities\Module\Auth\Model\User;
use CodeHuiter\Facilities\SearchList\SearchListResult;
use CodeHuiter\Facilities\Service\Compressor;

/**
 * The base facilities controller
 *
 * @property-read Compressor $comprewssor
 * @see FacilitiesConfig::INJECTED_COMPRESSOR There are Forward Usages
 *
 * @property-read \App\Service\Links $links
 * @see FacilitiesConfig::INJECTED_LINKS There are Forward Usages
 *
 * @property-read \CodeHuiter\Facilities\Service\Content $content
 * @see FacilitiesConfig::INJECTED_CONTENT There are Forward Usages
 *
 * @property-read \CodeHuiter\Facilities\Module\Auth\AuthService $auth
 * @see FacilitiesConfig::INJECTED_AUTH There are Forward Usages
 *
 * @property-read \CodeHuiter\Facilities\Module\Auth\UserService $userService
 * @see FacilitiesConfig::INJECTED_USER There are Forward Usages
 *
 * @property-read \CodeHuiter\Facilities\Service\AjaxResponse $ajaxResponse
 * @see FacilitiesConfig::INJECTED_AJAX_RESPONSE There are Forward Usages
 *
 * @property-read \CodeHuiter\Facilities\Service\Validator $validator
 * @see FacilitiesConfig::INJECTED_VALIDATOR There are Forward Usages
 */
class BaseController extends Controller
{
    /** @var array $data */
    protected $data;

    public $runData = [];

    public function __construct(Application $app)
    {
        parent::__construct($app);

        $this->init();
    }

    protected function errorPageByCode($code, $message = '')
    {
        try {
            $this->log->withTag('exceptions')->warning('Page '. $code .' showed with uri ['.$this->request->getUri().']', []);

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
        $this->data['template'] = VIEW_PATH . $this->app->config->projectConfig->template;
        $this->data['headAfterTpl'] = $this->app->config->projectConfig->headAfterTpl;
        $this->data['bodyAfterTpl'] = $this->app->config->projectConfig->bodyAfterTpl;
        $this->data['contentTpl'] = $contentTpl;

        $this->renderer->render($this->app->config->projectConfig->baseTemplatePath . '/baseMainTemplate', $this->data, $return);
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
            AuthService::GROUP_AUTH_SUCCESS => function(/** @noinspection PhpUnusedParameterInspection */ User $user) use ($those) {
                // User Not authed
                if ($this->ajaxResponse->isAjaxRequested($this->request)) {
                    $this->data['in_popup'] = true;
                    $this->ajaxResponse->openPopupWithData(
                        $this->renderer->render($this->app->config->authConfig->viewsPath. 'login', $this->data, true),
                        'authPopup',
                        ['maxWidth' => 600, 'close' => true,]
                    )->render($this->response);
                } else {
                    $addUrl = ($those->request->getUri()) ? '?url=' . urlencode($those->request->getUri()) : '';
                    $those->response->location($those->auth->config->urlAuth . $addUrl, true);
                }
            },
            AuthService::GROUP_NOT_BANNED => function(/** @noinspection PhpUnusedParameterInspection */ User $user) use ($those) {
                // User banned
                if ($this->ajaxResponse->isAjaxRequested($this->request)) {
                    $this->ajaxResponse
                        ->errorMessage($this->lang->get('auth:user_banned'))
                        ->closePopups()
                        ->render($this->response);
                } else {
                    /** TODO Implement this page */
                    $those->response->location($those->auth->config->urlBan, true);
                }
            },
            AuthService::GROUP_ACTIVE => function(/** @noinspection PhpUnusedParameterInspection */ User $user) use ($those) {
                // User banned
                if ($this->ajaxResponse->isAjaxRequested($this->request)) {
                    $this->ajaxResponse
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
        $this->lang->setLanguage($this->app->config->webConfig->language);
        $this->runData = [
            'bodyAjax' => $this->ajaxResponse->isBodyAjax($this->request),
            'language' => $this->app->config->webConfig->language,
        ];
        $this->data = [
            'siteUrl' => $this->app->config->webConfig->siteUrl,
            'filters' => [],
            'pages' => [],
        ];
        foreach($this->app->config->projectConfig->dataDefault as $defaultField) {
            $this->data[$defaultField] = $this->app->config->projectConfig->$defaultField;
        }

        $this->lang->setLanguage($this->runData['language']);
    }

    protected function acceptSearchListResult(SearchListResult $result, string $itemsKey = 'items'): void
    {
        $this->data['filters'] = $result->getFilters();
        $this->data['pages'] = $result->getPages();
        $this->data['pages']['total'] = $result->getItemsCount();
        $this->data[$itemsKey] = $result->getItems();
    }
}
