<?php

namespace CodeHuiter\Config;

use CodeHuiter\Core\Application;
use CodeHuiter\Core\Request;
use CodeHuiter\Core\Response;
use CodeHuiter\Config\Module\RelationalRepositoryConfig;
use CodeHuiter\Facilities\Module\Auth\Model\User;
use CodeHuiter\Facilities\Module\Auth\UserService;
use CodeHuiter\Facilities\Module\Connector\ConnectAccessibility;
use CodeHuiter\Facilities\Module\Connector\ConnectorService;
use CodeHuiter\Facilities\Module\Developing\DevelopingService;
use CodeHuiter\Facilities\Module\Media\Event\MediaSubscriber;
use CodeHuiter\Facilities\Module\Media\MediaService;
use CodeHuiter\Facilities\Module\Media\Model\Media;
use CodeHuiter\Facilities\Module\Media\Model\MediaRepository;
use CodeHuiter\Facilities\Module\ThirdPartyApi\ThirdPartyApiProvider;
use CodeHuiter\Facilities\Service\ByDefault;
use CodeHuiter\Facilities\Service\AjaxResponse;
use CodeHuiter\Facilities\Service\Validator;
use CodeHuiter\Service\ByDefault\PhpRenderer;
use CodeHuiter\Service\DateService;
use CodeHuiter\Facilities\Module\Auth\AuthService;
use CodeHuiter\Facilities\Module\Auth\Model\UserRepository;
use CodeHuiter\Facilities\Service\Compressor;
use CodeHuiter\Facilities\Service\Link;
use CodeHuiter\Facilities\Service\Content;
use CodeHuiter\Service\FileStorage;
use CodeHuiter\Service\Language;
use CodeHuiter\Service\Logger;

class FacilitiesConfig extends CoreConfig
{
    /** @var ProjectConfig */
    public $projectConfig;

    /** @var CompressorConfig */
    public $compressorConfig;
    /** @var LinksConfig */
    public $linksConfig;
    /** @var ContentConfig */
    public $contentConfig;
    /** @var AuthConfig */
    public $authConfig;
    /** @var ConnectorConfig */
    public $connectorConfig;
    /** @var MediaConfig */
    public $mediaConfig;

    // Injected services into controller
    public const INJECTED_COMPRESSOR = 'compressor';
    public const INJECTED_AJAX_RESPONSE = 'ajaxResponse';
    public const INJECTED_VALIDATOR = 'validator';
    public const INJECTED_LINKS = 'links';
    public const INJECTED_CONTENT = 'content';
    public const INJECTED_AUTH = 'auth';
    public const INJECTED_USER = 'userService';

    /**
     * @var RelationalRepositoryConfig[]
     */
    public $repositoryConfigs = [];

    public function __construct()
    {
        parent::__construct();

        $this->projectConfig = new ProjectConfig();
        $this->compressorConfig = new CompressorConfig();
        $this->linksConfig = new LinksConfig();
        $this->contentConfig = new ContentConfig();
        $this->authConfig = new AuthConfig();
        $this->connectorConfig = new ConnectorConfig();
        $this->mediaConfig = new MediaConfig();

        /**
         * Facility Services
         */
        $this->services[Compressor::class] = [
            self::KEY_CALLBACK => static function (Application $app) {
                return new ByDefault\Compressor(
                    $app->config->compressorConfig,
                    $app->get(Request::class),
                    $app->get(PhpRenderer::class)
                );
            },
            self::KEY_SCOPE => self::SCOPE_REQUEST,
        ];
        $this->services[AjaxResponse::class] = [
            self::KEY_CALLBACK => static function (Application $app) {
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
            self::KEY_SCOPE => self::SCOPE_REQUEST,
        ];
        $this->services[Link::class] = [
            self::KEY_CALLBACK => static function (Application $app) {
                return new Link($app, $app->config->linksConfig);
            }
        ];
        $this->services[Content::class] = [
            self::KEY_CALLBACK => static function (Application $app) {
                return new Content($app->config->contentConfig, $app->get(FileStorage::class), $app->get(Logger::class));
            }
        ];
        $this->services[Validator::class] = [
            self::KEY_CALLBACK => static function (Application $app) {
                return new ByDefault\Validator($app->get(Language::class));
            }
        ];


        /**
         * Module Services
         */
        $this->services[AuthService::class] = [
            self::KEY_CALLBACK => static function (Application $app) {
                return new AuthService(
                    $app,
                    $app->config->authConfig,
                    $app->get(DateService::class),
                    $app->get(Language::class),
                    $app->get(Request::class),
                    $app->get(Response::class),
                    $app->get(UserRepository::class)
                );
            },
            self::KEY_SCOPE => self::SCOPE_REQUEST,
        ];
        $this->services[ConnectorService::class] = [
            self::KEY_CALLBACK => static function (Application $app) {
                return new ConnectorService($app, $app->config->connectorConfig);
            },
        ];
        $this->services[ConnectAccessibility::class] = [
            self::KEY_CALLBACK => static function (Application $app) {
                return new ConnectAccessibility($app);
            },
        ];
        $this->services[DevelopingService::class] = [self::KEY_CLASS => DevelopingService::class, self::KEY_SCOPE => self::SCOPE_REQUEST,];
        $this->services[UserService::class] = [self::KEY_CLASS_APP => UserService::class, self::KEY_SCOPE => self::SCOPE_REQUEST,];
        $this->services[ThirdPartyApiProvider::class] = [self::KEY_CLASS_APP => ThirdPartyApiProvider::class, self::KEY_SCOPE => self::SCOPE_REQUEST];
        $this->services[MediaService::class] = [
            self::KEY_CALLBACK => static function (Application $app) {
                return new MediaService($app);
            },
        ];
        $this->services[MediaSubscriber::class] = [self::KEY_CLASS_APP => MediaSubscriber::class, self::KEY_SCOPE => self::SCOPE_REQUEST];

        /**
         * Repositories
         */
        $this->services[UserRepository::class] = [self::KEY_CLASS_APP => UserRepository::class, self::KEY_SCOPE => self::SCOPE_REQUEST,];
        $this->services[MediaRepository::class] = [self::KEY_CLASS_APP => MediaRepository::class, self::KEY_SCOPE => self::SCOPE_REQUEST,];

        /**
         * Injected to controller
         */
        $this->injectedServices[self::INJECTED_COMPRESSOR] = Compressor::class;
        $this->injectedServices[self::INJECTED_AJAX_RESPONSE] = AjaxResponse::class;
        $this->injectedServices[self::INJECTED_LINKS] = Link::class;
        $this->injectedServices[self::INJECTED_CONTENT] = Content::class;
        $this->injectedServices[self::INJECTED_VALIDATOR] = Validator::class;
        $this->injectedServices[self::INJECTED_AUTH] = AuthService::class;
        $this->injectedServices[self::INJECTED_USER] = UserService::class;


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

        $this->authConfig->cookieDomain = '.' . $this->settingsConfig->domain;

        $this->repositoryConfigs[User::class] = new RelationalRepositoryConfig(
            User::class, self::SERVICE_DB_DEFAULT, 'users', 'id', ['id']
        );
        $this->repositoryConfigs[Media::class] = new RelationalRepositoryConfig(
            Media::class, self::SERVICE_DB_DEFAULT, 'users', 'id', ['id']
        );

        $this->eventsConfig->events[] = [AuthConfig::EVENT_USER_JOIN_ACCOUNT, MediaSubscriber::class];
        $this->eventsConfig->events[] = [EventsConfig::modelUpdatedName(Media::class), MediaSubscriber::class];

        $this->routerConfig->routes['users/id(:num)'] = 'users/get/$1';
        $this->routerConfig->routes['users/id(:num)/medias'] = 'medias/media_list/$1';
        $this->routerConfig->routes['users/id(:num)/albums'] = 'medias/album_list/$1';
        $this->routerConfig->routes['users/id(:num)/album(:num)'] = 'medias/album_view/$1/$2';
        $this->routerConfig->routes['users/id(:num)/album(:num)/edit'] = 'users/album_edit/$1/$2';
    }
}

class ProjectConfig
{
    public $baseTemplatePath = SYSTEM_PATH . 'Facilities/View/Base/'; // Copy to App Views for custom views
    public $template = 'myTemplate/';
    public $headAfterTpl = 'head_after';
    public $bodyAfterTpl = 'body_after';
    public $pageStyle = 'default'; //'backed';

    public $dataDefault = ['headTitle', 'headDescription', 'headKeywords', 'headImage',];

    public $headTitle = 'Мой CodeHuiter Facilities';
    public $headDescription = 'My Simple Descripption';
    public $headKeywords = 'CodeHuiter Framework Facilities';
    public $headImage = '/pub/images/logo.png';

    public $projectName = 'CodeHuiter Facilities';
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

class ConnectorConfig
{
    public const TYPE_TEMP = 'temp';
    public const TYPE_PROFILE = 'profile';
    public const TYPE_MEDIA = 'media';

    public const TYPE_PHOTO = 'photo';
    public const TYPE_ALBUM = 'album';

    public $connectObjectRepositories = [
        self::TYPE_PROFILE => UserRepository::class
    ];
}

class MediaConfig
{
    public $viewsPath = SYSTEM_PATH . 'Facilities/Module/Media/View/'; // Copy to App Views for custom views

    public $watermark = [
        // Set null if not need watermark
        'png' => 'moponline-water.png',
        'png_percent' => 10,
        'png_x_position' => 'right',
        'png_y_position' => 'bottom',
    ];
}

class ContentConfig
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
    public $viewsPath = SYSTEM_PATH . 'Facilities/Module/Auth/View/'; // Copy to App Views for custom views
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

    /** @var GoogleApiConfig  */
    public $googleConfig;

    public function __construct()
    {
        $this->googleConfig = new GoogleApiConfig();
    }
}

class GoogleApiConfig
{
    public $googleApiKey = 'AIaaSyBCqqqqGsSS00iiD80mBmXN40_mTmAAAA0';
    public $googleAppId = '33111222333.apps.googleusercontent.com';
    public $googleSecret = 'KKcPPPw9--OwwKK0EE66VvVR';
}