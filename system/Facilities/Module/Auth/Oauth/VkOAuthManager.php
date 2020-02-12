<?php

namespace CodeHuiter\Facilities\Module\Auth\Oauth;

use CodeHuiter\Core\Application;
use CodeHuiter\Modifier\StringModifier;
use CodeHuiter\Facilities\Module\Auth\Model\User;
use CodeHuiter\Facilities\Module\Auth\Model\UserRepository;
use CodeHuiter\Service\Logger;
use CodeHuiter\Service\Network;

class VkOAuthManager implements OAuthManager
{
    private const CALLBACK_SUCCESS_REDIRECT = '/auth/oauth_success/vk'; /* call function login */
    private const CALLBACK_FAIL_REDIRECT = '/auth/oauth_cancel/vk'; /* call function login */

    private const LOGGER_TAG = 'VK_OAUTH';

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
     * @var string|null
     */
    private $iframeAppId;

    /**
     * @var string|null
     */
    private $iframeSecret;

    /**
     * @var string
     */
    private $permissions = '';
    /**
     * @var string
     */
    private $lastErrorMessage = '';
    /**
     * @var array
     */
    private $permissionsAvailable = [
        'notify','friends','photos','audio','video','docs','notes','pages'
        ,'status','offers','questions','wall','groups','messages', 'notifications'
        ,'stats','ads','offline','nohttps',
    ];

    private $genderMapping = [
        0 => OAuthData::GENDER_UNKNOWN,
        1 => OAuthData::GENDER_FEMALE,
        2 => OAuthData::GENDER_MALE,
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
        string $secret,
        ?string $iframeAppId,
        ?string $iframeSecret
    ) {
        $this->network = $network;
        $this->logger = $logger;
        $this->siteUrl = $siteUrl;
        $this->appId = $appId;
        $this->secret = $secret;
        $this->iframeAppId = $iframeAppId;
        $this->iframeSecret = $iframeSecret;
    }

    public function addPermission(array $permissionNames): void
    {
        foreach($permissionNames as $perm){
            if (in_array($perm, $this->permissionsAvailable, true)){
                if ($this->permissions !== '') {
                    $this->permissions .= ',';
                }
                $this->permissions .= $perm;
            }
        }
    }

    public function iframeUser(int $viewerId = 0, $authKey = ''): int
    {
        if ($authKey === md5($this->iframeAppId.'_'.$viewerId.'_'.$this->iframeSecret)) {
            return $viewerId;
        }
        return null;
    }

    public function getSourceAccessLink(): string
    {
        $successCallbackUrl = urlencode($this->siteUrl . self::CALLBACK_SUCCESS_REDIRECT);
        $url = 'https://oauth.vk.com/authorize?';
        $url .= 'client_id=' . $this->appId;
        $url .= '&scope=' . $this->permissions;
        $url .= '&redirect_uri=' . $successCallbackUrl;
        $url .= '&response_type=code';
        return $url;
    }

    public function login(array $getParams): ?OAuthData
    {
        $code = $getParams['code'] ?? '';
        if (!$code) {
            $this->lastErrorMessage = 'VK login fail! ' . ($getParams['error'] ?? '') . ': ' . ($getParams['error_description'] ?? '') . '.';
            return null;
        }
        $resp = $this->network->httpRequest(
            'https://oauth.vk.com/access_token',
            Network::METHOD_POST,
            [
                'client_id' => $this->appId,
                'client_secret' => $this->secret,
                'code' => $code,
                'redirect_uri' => $this->siteUrl . self::CALLBACK_SUCCESS_REDIRECT,
            ],
            ['Content-type:application/x-www-form-urlencoded']
        );
        $responseDecoded = StringModifier::jsonDecode($resp);
        $expire_in = $responseDecoded['expires_in'] ?? null;

        $userId = $responseDecoded['user_id'] ?? '';
        $accessToken = $responseDecoded['access_token'] ?? '';

        if (!$userId || !$accessToken) {
            $this->lastErrorMessage = 'VK login fail! ' . ($getParams['error'] ?? '') . ': ' . ($getParams['error_description'] ?? '') . '.';
            return null;
        }

        $authData = $this->getUserData($userId, $accessToken);

        if ($expire_in !== null && $authData) {
            $expire_in = (int)$expire_in;
            if ($expire_in === 0) {
                $authData->setAccessToken($accessToken);
            }
        }
        return $authData;
    }

    public function getUserData(string $user_id, ?string $access_token = null): ?OAuthData
    {
        $fields = urlencode('sex,bdate,photo_big,city,contacts,photo_max_orig');
        $access_token_str = $access_token ? '&access_token=' . $access_token : '';
        $resp = $this->network->httpRequest(
            'https://api.vk.com/method/users.get?uids='.$user_id.'&fields='.$fields.''.$access_token_str,
            Network::METHOD_GET
        );
        $userdata = StringModifier::jsonDecode($resp);
        $userdata = $userdata['response'][0] ?? [];
        $uid = $userdata['uid'] ?? '';
        if (!$uid) {
            $this->logger->withTag(self::LOGGER_TAG)->notice('VK GetUserData is invalid');
            $this->lastErrorMessage = 'VK login fail! Code 3.';
            return null;
        }
        $name = '';
        $firstName = $userdata['first_name'] ?? '';
        $lastName = $userdata['last_name'] ?? '';
        $gender = $this->genderMapping[(int)($userdata['sex'] ?? 0)] ?? OAuthData::GENDER_UNKNOWN;
        $profilePhoto = $userdata['photo_max_orig'] ?? '';
        $vkBirthday = $userdata['bdate'] ?? '';
        $profileBirthday = $vkBirthday ? StringModifier::dateConvert($vkBirthday, 'ru-m') : '0000-01-01';

        return new OAuthData(
            'vk',
            $uid,
            $name,
            $firstName,
            $lastName,
            $profilePhoto,
            $profileBirthday,
            $gender,
            []
        );
    }

    public function api(User $user, string $method, array $params): ?array
    {
        $dataInfo = $user->getDataInfo();
        $accessToken = $dataInfo['oauthData']['vk']['accessToken'] ?? '';
        if (!$accessToken) {
            $this->lastErrorMessage = 'No Access token for API request';
            return null;
        }
        $paramStr = '';
        if ($params){
            $buildParams = [];
            foreach($params as $key => $value){
                $buildParams[] = $key . '=' . urlencode($value);
            }
            $paramStr = implode('&', $buildParams);
        }
        if ($accessToken){
            if ($paramStr) {
                $paramStr .= '&';
            }
            $paramStr .= 'access_token=' . $accessToken;
        }

        $resp = $this->network->httpRequest(
            'https://api.vk.com/method/' . $method . '?' . $paramStr,
            Network::METHOD_GET
        );
        $response = StringModifier::jsonDecode($resp);
        if (isset($response['error']) && $response['error']) {
            $errorCode = (int)($response['error']['error_code'] ?? '');
            if (in_array($errorCode, [5, 10], true)) {
                // We need reset the token
                $dataInfo['oauthData']['vk']['accessToken'] = '';
                $user->setDataInfo($dataInfo);
                /** @var UserRepository $userRepositoryInterface */
                $userRepositoryInterface = Application::getInstance()->get(UserRepository::class);
                $userRepositoryInterface->save($user);

                $this->lastErrorMessage = 'Token is expired';
                return null;
            }
        }
        return $response;
    }
}

