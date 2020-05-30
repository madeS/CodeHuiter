<?php

namespace CodeHuiter\Config\Facilities;

use CodeHuiter\Config\FacilitiesConfig;

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

    public static function populateConfig(FacilitiesConfig $config): void
    {
        $config->projectConfig = new self();

    }

}