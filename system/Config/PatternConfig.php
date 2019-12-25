<?php

namespace CodeHuiter\Config;

use CodeHuiter\Core\Application;
use CodeHuiter\Core\Request;
use CodeHuiter\Core\Response;
use CodeHuiter\Pattern\Module\Auth\UserService;
use CodeHuiter\Pattern\Module\Developing\DevelopingService;
use CodeHuiter\Pattern\Service\ByDefault;
use CodeHuiter\Pattern\Service\AjaxResponse;
use CodeHuiter\Pattern\Service\Validator;
use CodeHuiter\Service\ByDefault\PhpRenderer;
use CodeHuiter\Service\DateService;
use CodeHuiter\Pattern\Module\Auth\AuthService;
use CodeHuiter\Pattern\Module\Auth\Model\UserModelRepository;
use CodeHuiter\Pattern\Module\Auth\Model\UserRepositoryInterface;
use CodeHuiter\Pattern\Service\Compressor;
use CodeHuiter\Pattern\Service\Link;
use CodeHuiter\Pattern\Service\Media;
use CodeHuiter\Service\Language;

class PatternConfig extends Config
{
    /** @var ProjectConfig */
    public $projectConfig;

    /** @var CompressorConfig */
    public $compressorConfig;
    /** @var LinksConfig */
    public $linksConfig;
    /** @var MediaConfig */
    public $mediaConfig;
    /** @var AuthConfig */
    public $authConfig;

    public const SERVICE_KEY_COMPRESSOR = 'compressor';
    public const SERVICE_KEY_MJSA_RESPONSE = 'ajaxResponse';
    public const SERVICE_KEY_VALIDATOR = 'validator';
    public const SERVICE_KEY_LINKS = 'links';
    public const SERVICE_KEY_MEDIA = 'media';
    public const SERVICE_KEY_AUTH = 'auth';
    public const SERVICE_KEY_USER = 'userService';

    public function __construct()
    {
        parent::__construct();

        $this->projectConfig = new ProjectConfig();

        /**
         * Compressor Service
         */
        $this->services[Compressor::class] = [
            self::OPT_KEY_CALLBACK => static function (Application $app) {
                return new ByDefault\Compressor(
                    $app->config->compressorConfig,
                    $app->get(Request::class),
                    $app->get(PhpRenderer::class)
                );
            },
            self::OPT_KEY_SCOPE => self::OPT_KEY_SCOPE_REQUEST,
        ];
        $this->injectedServices[self::SERVICE_KEY_COMPRESSOR] = Compressor::class;
        $this->compressorConfig = new CompressorConfig();
        // connect image crop (jcrop)
        $this->compressorConfig->css[] = '/pub/css/jquery.jcrop.min.css';
        $this->compressorConfig->js[] = '/pub/js/jquery.jcrop.min.js';
        // connect audio (jplayer)
        $this->compressorConfig->js[] = '/pub/js/jplayer/jquery.jplayer.min.js';
        // fancybox
        $this->compressorConfig->singlyCss[] = '/pub/plugins/fancybox/jquery.fancybox.css';
        $this->compressorConfig->singlyJs['fancybox'] = '/pub/plugins/fancybox/jquery.fancybox.pack.js';
        // select2
        $this->compressorConfig->singlyCss[] = '/pub/plugins/select2/select2.css';
        $this->compressorConfig->singlyJs['select2'] = '/pub/plugins/select2/select2.js';
        // tiny
        $this->compressorConfig->singlyJs['tinymce'] = '/pub/plugins/tinymce/tinymce.min.js';
        // application js
        $this->compressorConfig->css[] = '/pub/css/app.css.tpl.php';
        $this->compressorConfig->js[] = '/pub/js/app.js';
        // app.jplayer
        $this->compressorConfig->js[] = '/pub/js/app.jplayer.js';
        // app.dialogues
        $this->compressorConfig->css[] = '/pub/css/app.dialogues.css';
        $this->compressorConfig->js[] = '/pub/js/app.dialogues.js';
        // app.comments
        $this->compressorConfig->css[] = '/pub/css/app.comments.css';
        $this->compressorConfig->js[] = '/pub/js/app.comments.js';
        // app.custom
        $this->compressorConfig->js[] = '/pub/js/app.custom.js';
        // yashare
        $this->compressorConfig->singlyJs['yashare'] = '//yastatic.net/share/share.js" charset="utf-8';
        $this->compressorConfig->js[] = '/pub/js/app.yashare.js';

        /**
         * MjsaResponse Service
         */
        $this->services[AjaxResponse::class] = [
            self::OPT_KEY_CALLBACK => static function (Application $app) {
                /** @var Request $request */
                $request = $app->get(Request::class);
                if ($request->getRequestValue('mjsaAjax') || $request->getRequestValue('bodyAjax')) {
                    return new ByDefault\MjsaAjaxResponse($app->get(Language::class));
                }
                if ($request->getRequestValue('jsonAjax') || $request->getRequestValue('bodyJsonAjax')) {
                    return new ByDefault\JsonAjaxResponse($app->get(Language::class));
                }
                return new ByDefault\JsonAjaxResponse($app->get(Language::class));
            },
            self::OPT_KEY_SCOPE => self::OPT_KEY_SCOPE_REQUEST,
        ];
        $this->injectedServices[self::SERVICE_KEY_MJSA_RESPONSE] = AjaxResponse::class;

        /**
         * Links Service
         */
        $this->services[Link::class] = [self::OPT_KEY_CALLBACK => static function (Application $app) {
                return new Link($app, $app->config->linksConfig);
        }];
        $this->injectedServices[self::SERVICE_KEY_LINKS] = Link::class;
        $this->linksConfig = new LinksConfig();

        /**
         * Media Service
         */
        $this->services[Media::class] = [self::OPT_KEY_CALLBACK => static function (Application $app) {
            return new Media($app->config->mediaConfig);
        }];
        $this->injectedServices[self::SERVICE_KEY_MEDIA] = Media::class;
        $this->mediaConfig = new MediaConfig();

        /**
         * Validator Service
         */
        $this->services[Validator::class] = [self::OPT_KEY_CALLBACK => static function (Application $app) {
            return new ByDefault\Validator($app->get(Language::class));
        }];
        $this->injectedServices[self::SERVICE_KEY_VALIDATOR] = Validator::class;

        /**
         * Auth Service
         */
        $this->services[AuthService::class] = [
            self::OPT_KEY_CALLBACK => static function (Application $app) {
                return new AuthService(
                    $app,
                    $app->config->authConfig,
                    $app->get(DateService::class),
                    $app->get(Language::class),
                    $app->get(Request::class),
                    $app->get(Response::class),
                    $app->get(UserRepositoryInterface::class)
                );
            },
            self::OPT_KEY_SCOPE => self::OPT_KEY_SCOPE_REQUEST,
        ];
        $this->injectedServices[self::SERVICE_KEY_AUTH] = AuthService::class;
        $this->authConfig = new AuthConfig();
        $this->authConfig->cookieDomain = '.' . $this->settingsConfig->domain;

        $this->services[DevelopingService::class] = [self::OPT_KEY_CLASS => DevelopingService::class, self::OPT_KEY_SCOPE => self::OPT_KEY_SCOPE_REQUEST,];

        /**
         * UserService
         */
        $this->services[UserService::class] = [self::OPT_KEY_CLASS_APP => UserService::class, self::OPT_KEY_SCOPE => self::OPT_KEY_SCOPE_REQUEST,];
        $this->injectedServices[self::SERVICE_KEY_USER] = UserService::class;

        /**
         * UserRepository
         */
        $this->services[UserRepositoryInterface::class] = [self::OPT_KEY_CLASS_APP => UserModelRepository::class, self::OPT_KEY_SCOPE => self::OPT_KEY_SCOPE_REQUEST,];
    }
}

class ProjectConfig
{
    public $baseTemplatePath = SYSTEM_PATH . 'Pattern/View/Base/'; // Copy to App Views for custom views
    public $template = 'myTemplate/';
    public $headAfterTpl = 'head_after';
    public $bodyAfterTpl = 'body_after';
    public $pageStyle = 'default'; //'backed';

    public $dataDefault = ['headTitle', 'headDescription', 'headKeywords', 'headImage',];

    public $headTitle = 'Мой CodeHuiter Pattern';
    public $headDescription = 'My Simple Descripption';
    public $headKeywords = 'CodeHuiter Framework Pattern';
    public $headImage = '/pub/images/logo.png';

    public $projectName = 'CodeHuiter Pattern';
    public $projectLogo = '';
    public $projectYear = 2016;
    public $projectCompany = 'МайКомпани';

    public $copyrightName = 'Andrei Bogarevich';

    public $developingUrl = 'http://bogarevich.com/production';
    public $developingTitle = 'Andrei Bogarevich';
    public $developingName = 'Andrei Bogarevich';

    public $supportUserId = 1;

    public $usersViewSocialOriginLinks = false;

    public $disableDbImport = true;
}

class CompressorConfig
{
    public $version = 'dev'; // OR some time for cache
    public $dir = '/pub/compressor';
    public $names = 'compressed';
    public $css = [
        //'http://code.jquery.com/ui/1.9.2/themes/base/jquery-ui.css',
        '/pub/css/mjsa.css',
        '/pub/plugins/jqueryui/jquery-ui.min.css',
    ];
    public $js = [
        '/pub/js/jquery-3.1.1.min.js',
        '/pub/plugins/jqueryui/jquery-ui.min.js',
        '/pub/js/mjsa.js',
    ];
    public $resultCss = '';
    public $resultJs = '';
    public $singlyCss = [];
    public $singlyJs = [];
    public $domainCompressor = ['sub.app.local' => null];
}

class LinksConfig
{
    public $aliases = [
        'users' => '/users',
        'user_view' => '/users/id{#param}',
        'user_settings' => '/users/settings',
        'user_medias' => '/users/id{#param}/medias',
        'messages' => '/messages',
        'messages_user' => '/messages/user{#param}',
        'messages_room' => '/messages/room{#param}',

        'blog_add' => '/blog/add',
        'blog_edit' => '/blog/edit/{#param}',
        'blog_page_categored' => '/page-{#param}/{#param}',
        'blog_page' => '/page-{#param}',

        'user_albums' => '/users/id{#param}/albums',
        'user_album' => '/users/id{#param}/album{#param}',
        'user_album_edit' => '/users/id{#param}/album{#param}/edit',
    ];
}

class MediaConfig
{
    public $storageMap = [
        'watermarks' => [
            'store' => '/pub/images/watermarks/',
            'server_root' => PUB_PATH,
            'site_url' => '',
        ],
        'user_medias' => [
            'store' => '/pub/files/images/user_medias/',
            'server_root' => PUB_PATH,
            'site_url' => '',
        ],
        'example_cloud' => [
            'store' => '/cloud_folder/{#locale}/',
            'server_root' => '/home/disk1',
            'site_url' => 'http://asset.example.com',
        ],
    ];
}

class AuthConfig
{
    public const EVENT_USER_GROUP_CHANGED = 'user.groupChanged';
    public const EVENT_USER_JOIN_ACCOUNT = 'user.joinAccount';
    public const EVENT_USER_DELETING = 'user.deleting';

    public $salt = '';
    public $passFuncMethod = 'normal';
    public $logoutIfIpChange = false;       // не принимает sig  с другого ip, выкидывает с профиля, если ip сменился
    public $multiconnectAvailable = false;  // разрешает логиниться с нескольких браузеров, устройств.
    public $onlineTime = 180;               // Человек считается онлайн, количество секунд.
    public $cookieDays = 14;                // дни, время через которое должны стираться куки этого сайта (разлогинется в случае незахода)
    public $cookieDomain = '.app.local';    //'.bogarevich.com', // домен для cookie
    public $allowRegister = true;           // разрешается ли регистрация на сайтеDhtvz hfpразрешается ли регистрация на сайте
    public $nonactiveUpdateTime = 60;       // время раз в которое обновляется время последнего посещения
    public $viewsPath = SYSTEM_PATH . 'Pattern/Module/Auth/View/'; // Copy to App Views for custom views
    public $groups = [];

    public $emailQueued = true;
    public $emailForce = true;

    public $urlAuth = '/auth';
    public $urlBan = '/auth/banned';
    public $urlActive = '/auth/email_conf_sended';
    public $urlLogout = '/auth/logout';
    public $authEmailConfSended = '/auth/email_conf_sended';

    public $originSources = [
        'vk',
        'fb',
        'gl',
        'tw',
        'ig',
        'od',
    ];

    public $facebookAppId = '600000000000121';
    public $facebookSecret = '9aaabbbcccdddeeeeefff00011122233';
    public $facebookLocale = 'ru_RU';
    public $googleApiKey = 'AIaaSyBCqqqqGsSS00iiD80mBmXN40_mTmAAAA0';
    public $googleAppId = '33111222333.apps.googleusercontent.com';
    public $googleSecret = 'KKcPPPw9--OwwKK0EE66VvVR';
    public $instagramAppId = '9aaabbbcccdddeeeeefff00011122233';
    public $instagramSecret = '9aaabbbcccdddeeeeefff00011122233';
    public $vkAppId = '1222333';
    public $vkSecret = '1AA1A1AAAA11aAaaAaAA';
    public $vkIframeAppId = null;
    public $vkIframeSecret = null;
    public $twitterConsumerKey = 'F9fffFzz8zz7z6zz5zzXX';
    public $twitterConsumerSecret = '9aaabbbcccdddeeeeefff00011122233444555666RR';
    public $twitterAccessToken = '111111111-fFww1wWWWw00wWWWW0qQ0aa00aAAaA0AAAAAaAAa';
    public $twitterAccessTokenSecret = '9aaabbbcccdddeeeeefff00011122233444555666R';
    public $dropboxEmail = 'myemail@gmail.com';
    public $dropboxPassword = 'pppppppppp1111';

    public $pictureDefault = 'profile_nopicture'; // default/profile_nopicture.png + default/profile_nopicture_preview.png
    public $pictureBanned = 'profile_banned'; // default/profile_banned.png + default/profile_banned_preview.png
    public $pictureUnActive = 'profile_unactive'; // default/profile_unactive.png + default/profile_unactive_preview.png
}
