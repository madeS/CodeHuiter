<?php

namespace CodeHuiter\OldFrameworkAdapter\Service {

    class Load
    {
        public const SERVICE_NAME = 'CI_LOADER';

        public function models()
        {

        }
    }
}



namespace {

    use CodeHuiter\Config\Config;
    use CodeHuiter\Core\Application;
    use CodeHuiter\Service\Language;

    function lang($key): string
    {
        /** @var Language $lang */
        $lang = Application::getInstance()->get(Config::SERVICE_KEY_LANG);
        return $lang->get('quotar_old:'.$key);
    }
}
