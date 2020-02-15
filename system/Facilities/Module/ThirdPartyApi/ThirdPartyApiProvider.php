<?php

namespace CodeHuiter\Facilities\Module\ThirdPartyApi;

use CodeHuiter\Core\Application;
use CodeHuiter\Facilities\Module\ThirdPartyApi\Google\YouTubeApi;
use CodeHuiter\Service\Language;
use CodeHuiter\Service\Logger;
use CodeHuiter\Service\Network;

class ThirdPartyApiProvider
{
    /**
     * @var Application
     */
    private $application;

    /**
     * @var
     */
    private $cache;

    public function __construct(Application $application)
    {
        $this->application = $application;
    }

    public function getYoutubeApi(): YouTubeApi
    {
        $cacheKey = 'YoutubeApi';
        if (!isset($this->cache[$cacheKey])) {
            $this->cache[$cacheKey] = new YouTubeApi(
                $this->application->config->authConfig->googleConfig,
                $this->getNetwork(),
                $this->getLanguage(),
                $this->getLogger()
            );
        }
        return $this->cache[$cacheKey];
    }

    private function getNetwork(): Network
    {
        return $this->application->get(Network::class);
    }

    private function getLanguage(): Language
    {
        return $this->application->get(Language::class);
    }

    private function getLogger(): Logger
    {
        return $this->application->get(Logger::class);
    }
}
