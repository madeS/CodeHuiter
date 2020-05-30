<?php

namespace CodeHuiter\Facilities\Module\Auth\Oauth;

use CodeHuiter\Config\Facilities\Module\AuthConfig;
use CodeHuiter\Config\Core\WebConfig;
use CodeHuiter\Service\Logger;
use CodeHuiter\Service\Network;

class OAuthFactory
{
    /**
     * @var Network
     */
    private $network;
    /**
     * @var Logger
     */
    private $logger;
    /**
     * @var WebConfig
     */
    private $webConfig;
    /**
     * @var AuthConfig
     */
    private $authConfig;

    public function __construct(
        Network $network,
        Logger $logger,
        WebConfig $webConfig,
        AuthConfig $authConfig
    ) {
        $this->network = $network;
        $this->logger = $logger;
        $this->webConfig = $webConfig;
        $this->authConfig = $authConfig;
    }

    public function createOAuthManager(string $type): ?OAuthManager
    {
        switch ($type) {
            case 'vk': return new VkOAuthManager(
                $this->network,
                $this->logger,
                $this->webConfig->siteUrl,
                $this->authConfig->vkAppId,
                $this->authConfig->vkSecret,
                $this->authConfig->vkIframeAppId,
                $this->authConfig->vkIframeSecret
            );
            case 'fb': return new FbOAuthManager(
                $this->network,
                $this->logger,
                $this->webConfig->siteUrl,
                $this->authConfig->facebookAppId,
                $this->authConfig->facebookSecret,
                $this->authConfig->facebookSecret
            );
            case 'gl': return new GlOAuthManager(
                $this->network,
                $this->logger,
                $this->webConfig->siteUrl,
                $this->authConfig->googleConfig->googleAppId,
                $this->authConfig->googleConfig->googleSecret
            );
        }
        return null;
    }
}

