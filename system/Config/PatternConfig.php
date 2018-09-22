<?php

namespace CodeHuiter\Config;

class PatternConfig extends Config
{
    public function __construct()
    {
        parent::__construct();

        // array_merge ?
        $this->config['main'] = [
            'template' => 'pattern/',
            'pageStyle' => 'default', //'backed',

            'protocol' => 'http',
            'domain' => 'app.local',

            'language' => 'russian',

            'data_default' => ['head_title','head_description','head_keywords','head_image',],

            'head_title' => 'Мой CodeHuiter Pattern',
            'head_description' => 'My Simple Descripption',
            'head_keywords' => 'CodeHuiter Framework Pattern',
            'head_image' => '/pub/images/logo.png',

            'project_name' => 'CodeHuiter Pattern',
            'project_logo' => '',
            'project_year' => 2016,
            'project_company' => 'МайКомпани',

            'copyright_name' => 'Andrei Bogarevich',

            'developing_url' => 'http://bogarevich.com/production',
            'developing_title' => 'Andrei Bogarevich',
            'developing_name' => 'Andrei Bogarevich',
        ];

        $this->config['auth'] = [
            'logout_if_ip_change' => false,     // не принимает sig  с другого ip, выкидывает с профиля, если ip сменился
            'multiconnect_available' => false,  // разрешает логиниться с нескольких браузеров, устройств.
            'online_time' => 180,               // Человек считается онлайн, количество секунд.
            'cookie_days' => 14,                // дни, время через которое должны стираться куки этого сайта (разлогинется в случае незахода)
            'cookie_domain' => '.' . $this->config['main']['domain'], //'.bogarevich.com', // домен для cookie
            'allow_register' => true,           // разрешается ли регистрация на сайтеDhtvz hfpразрешается ли регистрация на сайте
            'nonactive_update_time' => 60,      // время раз в которое обновляется время последнего посещения
            'viewsPath' => '',                  //':', - for cusom auth views

            'url_auth' => '/auth',
            'url_ban' => '/auth/banned',
            'url_active' => '/auth/email_conf_sended',
            'auth_email_conf_sended' => '/auth/email_conf_sended',

            'facebook_app_id' => '600000000000121',
            'facebook_secret' => '9aaabbbcccdddeeeeefff00011122233',
            'facebook_locale' => 'ru_RU',
            'google_api_key' => 'AIaaSyBCqqqqGsSS00iiD80mBmXN40_mTmAAAA0',
            'google_app_id' => '33111222333.apps.googleusercontent.com',
            'google_secret' => 'KKcPPPw9--OwwKK0EE66VvVR',
            'instagram_app_id' => '9aaabbbcccdddeeeeefff00011122233',
            'instagram_secret' => '9aaabbbcccdddeeeeefff00011122233',
            'vk_app_id' => '1222333',
            'vk_secret' => '1AA1A1AAAA11aAaaAaAA',
            'twitter_consumer_key' => 'F9fffFzz8zz7z6zz5zzXX',
            'twitter_consumer_secret' => '9aaabbbcccdddeeeeefff00011122233444555666RR',
            'twitter_access_token' => '111111111-fFww1wWWWw00wWWWW0qQ0aa00aAAaA0AAAAAaAAa',
            'twitter_access_token_secret' => '9aaabbbcccdddeeeeefff00011122233444555666R',
            'dropbox_email' => 'myemail@gmail.com',
            'dropbox_password' => 'pppppppppp1111',
        ];

        $this->compressorConfig();
    }

    protected function compressorConfig()
    {
        $this->config['compressor'] = [
            //'version' => '201601151549',
            'version' => 'dev', // dev обновляется постоянно
            'css' => [
                //'http://code.jquery.com/ui/1.9.2/themes/base/jquery-ui.css',
                '/pub/css/mjsa.css',
                '/pub/plugins/jqueryui/jquery-ui.min.css',
            ],
            'js' => [
                '/pub/js/jquery-3.1.1.min.js',
                '/pub/plugins/jqueryui/jquery-ui.min.js',
                '/pub/js/mjsa.js',
            ],
            'dir' => '/pub/compressor',
            'names' => 'compressed',
            'singly' => [
                'css' => [],
                'js' => [],
            ]
        ];
        // connect image crop (jcrop)
        $this->config['compressor']['css'][] = '/pub/css/jquery.jcrop.min.css';
        $this->config['compressor']['js'][] = '/pub/js/jquery.jcrop.min.js';
        // connect audio (jplayer)
        $this->config['compressor']['js'][] = '/pub/js/jplayer/jquery.jplayer.min.js';
        // fancybox
        $this->config['compressor']['singly']['css'][] = '/pub/plugins/fancybox/jquery.fancybox.css';
        $this->config['compressor']['singly']['js']['fancybox'] = '/pub/plugins/fancybox/jquery.fancybox.pack.js';
        // select2
        $this->config['compressor']['singly']['css'][] = '/pub/plugins/select2/select2.css';
        $this->config['compressor']['singly']['js']['select2'] = '/pub/plugins/select2/select2.js';
        // tiny
        $this->config['compressor']['singly']['js']['tinymce'] = '/pub/plugins/tinymce/tinymce.min.js';
        // application js
        $this->config['compressor']['css'][] = '/pub/css/app.css.tpl.php';
        $this->config['compressor']['js'][] = '/pub/js/app.js';
        // app.jplayer
        $this->config['compressor']['js'][] = '/pub/js/app.jplayer.js';
        // app.dialogues
        $this->config['compressor']['css'][] = '/pub/css/app.dialogues.css';
        $this->config['compressor']['js'][] = '/pub/js/app.dialogues.js';
        // app.comments
        $this->config['compressor']['css'][] = '/pub/css/app.comments.css';
        $this->config['compressor']['js'][] = '/pub/js/app.comments.js';
        // app.custom
        $this->config['compressor']['js'][] = '/pub/js/app.custom.js';
        // yashare
        $this->config['compressor']['singly']['js']['yashare'] = '//yastatic.net/share/share.js" charset="utf-8';
        $this->config['compressor']['js'][] = '/pub/js/app.yashare.js';

        $this->config['compressor']['domain_compressor'] = [
            'sub.app.local' => [

            ]
        ];
    }

    public function initialize()
    {
        parent::initialize();
        $this->config['main']['site_url'] = $this->config['main']['protocol'] . '://' .  $this->config['main']['domain'];
    }
}
