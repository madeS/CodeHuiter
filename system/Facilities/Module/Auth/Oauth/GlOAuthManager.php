<?php

namespace CodeHuiter\Facilities\Module\Auth\Oauth;

use CodeHuiter\Modifier\StringModifier;
use CodeHuiter\Service\Logger;
use CodeHuiter\Service\Network;

class GlOAuthManager implements OAuthManager
{
    private const CALLBACK_SUCCESS_REDIRECT = '/auth/oauth_success/gl'; /* call function login */
    private const CALLBACK_FAIL_REDIRECT = '/auth/oauth_cancel/gl'; /* call function login */

    private const LOGGER_TAG = 'GL_OAUTH';

    /**
     * @var Network
     */
    private $network;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var string
     */
    private $siteUrl;

    /**
     * @var string
     */
    private $appId;

    /**
     * @var string
     */
    private $secret;

    /**
     * @var string
     */
    private $lastErrorMessage = '';

    private $genderMapping = [
        1 => OAuthData::GENDER_MALE,
        2 => OAuthData::GENDER_FEMALE,
    ];

    public function getLastErrorMessage(): string
    {
        return $this->lastErrorMessage;
    }


    public function __construct(
        Network $network,
        Logger $logger,
        string $siteUrl,
        string $appId,
        string $secret
    ) {
        $this->network = $network;
        $this->logger = $logger;
        $this->siteUrl = $siteUrl;
        $this->appId = $appId;
        $this->secret = $secret;
    }

    public function addPermission(array $permissions): void
    {
    }

    public function getSourceAccessLink(): string
    {
        $successCallbackUrl = urlencode($this->siteUrl . self::CALLBACK_SUCCESS_REDIRECT);
        $url = 'https://accounts.google.com/o/oauth2/auth?';
        $url .= 'redirect_uri='.$successCallbackUrl;
        $url .= '&response_type=code';
        $url .= '&client_id='.$this->appId;
        //$url .= '&scope=https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fuserinfo.profile';
        $url .= '&scope=https://www.googleapis.com/auth/userinfo.profile';
        //$url .= '&scope=https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fuserinfo.email+https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fuserinfo.profile';
        return $url;
    }

    public function login(array $getParams): ?OAuthData
    {
        $code = $getParams['code'] ?? '';
        if (!$code) {
            $this->lastErrorMessage = 'Google login fail! google not return auth code.';
            return null;
        }

        $resp = $this->network->httpRequest(
            'https://accounts.google.com/o/oauth2/token',
            Network::METHOD_POST,
            [
                'code' => $code,
                'client_id' => $this->appId,
                'client_secret' => $this->secret,
                'redirect_uri' => $this->siteUrl . self::CALLBACK_SUCCESS_REDIRECT,
                'grant_type' => 'authorization_code',
            ],
            ['Content-type:application/x-www-form-urlencoded']
        );
        $response = StringModifier::jsonDecode($resp);
        $accessToken = $acc_arr['access_token'] ?? '';

        if (!$accessToken) {
            $this->lastErrorMessage = 'Google login fail! google not return access token.';
            return null;
        }
        return $this->getUserData($accessToken);
    }

    public function getUserData(string $accessToken): ?OAuthData
    {
        $url = 'https://www.googleapis.com/oauth2/v1/userinfo?access_token=' . $accessToken . '';
        $resp = $this->network->httpRequest($url, Network::METHOD_GET);
        $userdata = StringModifier::jsonDecode($resp);
        $userId = $userdata['id'] ?? '';
        if (!$userId) {
            $this->lastErrorMessage = 'Google login fail! Code1003.';
            return null;
        }

        return new OAuthData(
            'google',
            $userId,
            $userdata['name'] ?? '',
            $userdata['given_name'] ?? '',
            $userdata['family_name'] ?? '',
            $userdata['picture'] ?? '',
            '0000-01-01',
            $this->genderMapping[$userdata['gender'] ?? ''] ?? OAuthData::GENDER_UNKNOWN,
            []
        );
    }
}
