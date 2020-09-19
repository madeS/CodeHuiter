<?php

namespace CodeHuiter\Config\Facilities\Module;

use CodeHuiter\Config\Core\ServiceConfig;
use CodeHuiter\Config\Database\DatabaseConfig;
use CodeHuiter\Config\FacilitiesConfig;
use CodeHuiter\Core\Application;
use CodeHuiter\Core\Request;
use CodeHuiter\Core\Response;
use CodeHuiter\Facilities\Module\Auth\AuthService;
use CodeHuiter\Facilities\Module\Auth\Model\User;
use CodeHuiter\Facilities\Module\Auth\Model\UserRepository;
use CodeHuiter\Facilities\Module\Auth\UserService;
use CodeHuiter\Service\DateService;
use CodeHuiter\Service\Language;

class AuthConfig
{
    public const EVENT_USER_GROUP_CHANGED = 'user.groupChanged';
    public const EVENT_USER_JOIN_ACCOUNT = 'user.joinAccount';

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
        'facebook',
        'google',
        'twitter',
        'instagram',
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

    /** @var GoogleApiConfig */
    public $googleConfig;

    public function __construct()
    {
        $this->googleConfig = new GoogleApiConfig();
    }

    public static function populateConfig(FacilitiesConfig $config): void
    {
        $config->authConfig = new self();
        $config->authConfig->cookieDomain = '.' . $config->webConfig->domain;

        $config->services[AuthService::class] = ServiceConfig::forCallback(
            static function (Application $app) {
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
            ServiceConfig::SCOPE_REQUEST
        );
        $config->services[UserService::class] = ServiceConfig::forAppClass(
            UserService::class,
            ServiceConfig::SCOPE_REQUEST
        );

        $config->services[UserRepository::class] = ServiceConfig::forAppClass(
            UserRepository::class,
            ServiceConfig::SCOPE_REQUEST
        );

        $config->databaseConfig->setRelational(User::class, DatabaseConfig::SERVICE_DB_DEFAULT, 'users', 'id', ['id']);
    }
}