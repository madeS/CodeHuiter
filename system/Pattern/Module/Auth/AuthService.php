<?php

namespace CodeHuiter\Pattern\Module\Auth;

use CodeHuiter\Config\AuthConfig;
use CodeHuiter\Core\Application;
use CodeHuiter\Core\Request;
use CodeHuiter\Core\Response;
use CodeHuiter\Exception\InvalidFlowException;
use CodeHuiter\Pattern\Module\Auth\Event\GroupsChangedEvent;
use CodeHuiter\Pattern\Module\Auth\Event\JoinAccountsEvent;
use CodeHuiter\Pattern\Module\Auth\Model\UserInterface;
use CodeHuiter\Pattern\Module\Auth\Model\UserRepositoryInterface;
use CodeHuiter\Pattern\Result\ClientResult;
use CodeHuiter\Service\DateService;
use CodeHuiter\Service\Mailer;
use CodeHuiter\Service\Language;
use CodeHuiter\Pattern\Service\Mjsa;

/**
 * TODO Add max 10 logins per minute
 */
class AuthService
{
    const MODULE_PATH = 'Pattern/Module/Auth/';

    private const PASS_FUNC_METHOD_NORMAL = 'normal';

    /** @var Application */
    protected $app;

    /** @var UserRepositoryInterface */
    protected $userRepository;

    /** @var DateService */
    protected $date = null;

    /** @var Language */
    protected $lang = null;

    /** @var Request */
    protected $request = null;

    /** @var Response  */
    protected $response = null;

    /** @var UserInterface */
    public $user = null;

    /** @var AuthConfig  */
    public $config;

    protected $lastErrorMessage;

    public const GROUP_AUTH_SUCCESS = 0;   // User Authed
    public const GROUP_NOT_BANNED = 1;     // Not banned user
    public const GROUP_NOT_DELETED = 2;    // User not delete yourself
    public const GROUP_ACTIVE = 3;         // Is activate by email or social network
    public const GROUP_MODERATOR = 5;      // Tagged as Moderator
    public const GROUP_ADMIN = 6;          // Tagged as Admin
    public const GROUP_SUPER_ADMIN = 7;    // Tagged as Super Admin

    public const TOKEN_TYPE_RECOVERY = 'recovery';
    public const TOKEN_TYPE_CONFIRM_EMAIL = 'email';


    protected $groups = [
        self::GROUP_NOT_BANNED,
        self::GROUP_NOT_DELETED,
        self::GROUP_ACTIVE,
        self::GROUP_MODERATOR,
        self::GROUP_ADMIN,
        self::GROUP_SUPER_ADMIN,
    ];

    protected $commonHash = '8dc66b2be10c6882c4565f74a2f9f21f';

    /**
     * @param Application $application
     * @param AuthConfig $config
     * @param DateService $dateService
     * @param Language $language
     * @param Request $request
     * @param Response $response
     * @param UserRepositoryInterface $userRepository
     */
    public function __construct(
        Application $application,
        AuthConfig $config,
        DateService $dateService,
        Language $language,
        Request $request,
        Response $response,
        UserRepositoryInterface $userRepository
    ) {
        $this->app = $application;
        $this->config = $config;
        $this->date = $dateService;
        $this->lang = $language;
        $this->request = $request;
        $this->response = $response;
        $this->userRepository = $userRepository;

        $this->groups = array_merge($this->groups, $this->config->groups); // Additional groups
    }

    /**
     * @return Mailer
     */
    protected function getEmail()
    {
        /** @var Mailer $email */
        $email = $this->app->get(Mailer::class);
        return $email;
    }

    protected function setErrorMessage($message)
    {
        $this->lastErrorMessage = $message;
        return false;
    }

    public function getErrorMessage()
    {
        return $this->lastErrorMessage;
    }

    public function getViewsPath()
    {
        return ($this->config->viewsPath)
            ? $this->config->viewsPath
            : SYSTEM_PATH . self::MODULE_PATH . 'View/';
    }

    /**
     * @param bool $require
     * @param array $requiredGroups
     * @param array $customActions
     * @return bool
     */
    public function initUser(
        $require = false,
        $requiredGroups = [
            self::GROUP_NOT_BANNED,
            self::GROUP_ACTIVE,
        ],
        $customActions = []
    ) {
        if (!$this->user || !$this->user->exist()) {
            // Not login? Try to recognize user.
            if (!$this->checkUser()) {
                //  User not login
                if (!$require) {
                    return true;
                }
                // Auth Action
                if (isset($customActions[self::GROUP_AUTH_SUCCESS])) {
                    // User auth action
                    $customActions[self::GROUP_AUTH_SUCCESS]($this->user);
                }
                return false;
            }
        }

        // User is authed

        $needAccessGroups = $this->userNotInGroups($this->user, $requiredGroups);
        if ($needAccessGroups) {
            // Have not required group
            if (in_array(self::GROUP_NOT_BANNED, $needAccessGroups, true)) {
                // User is banned
                if (isset($customActions[self::GROUP_NOT_BANNED])) {
                    // User ban action
                    $customActions[self::GROUP_NOT_BANNED]($this->user);
                }
                return $this->setErrorMessage($this->lang->get('auth_sign:user_is_banned'));
            }
            if (in_array(self::GROUP_ACTIVE, $needAccessGroups, true)) {
                // User is banned
                if (isset($customActions[self::GROUP_ACTIVE])) {
                    // User ban action
                    $customActions[self::GROUP_ACTIVE]($this->user);
                }
                return $this->setErrorMessage($this->lang->get('auth_sign:user_is_not_active'));
            }
        }
        return true;
    }


    /**
     * @param UserInterface $user
     * @param int[] $requiredGroups
     * @return int[]
     */
    protected function userNotInGroups(UserInterface $user, array $requiredGroups): array
    {
        $result = [];
        foreach ($requiredGroups as $requiredGroup) {
            if (!$user->isInGroup($requiredGroup)) {
                $result[] = $requiredGroup;
            }
        }
        return $result;
    }

    /**
     * Check user from cookie data.
     * If success user info will be recorded to $user
     * If not success
     * @return bool
     */
    protected function checkUser(): bool
    {
        $id = $this->request->getCookie('id');
        $sig = $this->request->getCookie('sig');
        if (!$id || !$sig) {
            $this->user = $this->getDefaultUser();
            return false;
        }
        $ui = $this->getUserInfo($id, $sig);
        if (!$ui) {
            $this->user = $this->getDefaultUser();
            return false;
        }
        $this->user = $ui;
        return true;
    }

    /**
     * @param $id
     * @param $sig
     * @return bool|UserInterface
     */
    protected function getUserInfo($id, $sig)
    {
        $userInfo = $id ? $this->getUserById($id) : false;
        if (!$userInfo) {
            return $this->setErrorMessage($this->lang->get('auth_sign:incorrect_id'));
        }

        if (isset($this->commonHash) && md5($sig) === $this->commonHash) {
            $userInfo->setGroups(array_merge($userInfo->getGroups(), $this->groups), false);
            return $userInfo;
        }
        if (!$sig || $sig === 'NULL' || $sig !== $userInfo->getSignature()) {
            return $this->setErrorMessage($this->lang->get('auth_ui:incorrect_sig'));
        }

        if ($this->config->logoutIfIpChange && $userInfo->getLastIp() !== $this->request->getClientIP()) {
            return $this->setErrorMessage($this->lang->get('auth:incorrect_ip'));
        }
        if ($this->date->now > $this->date->addDays($userInfo->getSignatureTime(), 1)) {
            // При мультиконнекте продлевает старый sig иначе создает новый и меняет
            $this->updateSig($userInfo);
        }
        if ($this->date->now - $userInfo->getLastActive() > $this->config->nonactiveUpdateTime) {
            $userInfo->setLastActive($this->date->now);
            $userInfo->saveUser();
        }
        return $userInfo;
    }

    /**
     * @param int $id
     * @return UserInterface|null
     */
    public function getUserById($id): ?UserInterface
    {
        return $this->userRepository->findOne(['id' => $id]);
    }

    /**
     * @return UserInterface
     */
    public function getDefaultUser(): UserInterface
    {
        return $this->userRepository->newInstance();
    }

    /**
     * @param UserInterface $userInfo
     */
    protected function updateSig(UserInterface $userInfo): void
    {
        $oldSig = '';
        if ($this->config->multiconnectAvailable) {
            $oldSig = $userInfo->getSignature();
        }

        $newSig = $this->sigFunc($userInfo->getId(), $userInfo->getLogin(), $userInfo->getEmail(), $userInfo->getPassHash());

        if ($oldSig && strlen($oldSig) > 5){
            $newSig = $oldSig;
        }

        $userInfo->setSignature($newSig);
        $userInfo->setSignatureTime($this->date->now);
        $userInfo->setLastIp($this->request->getClientIP());
        $userInfo->saveUser();

        $this->response->setCookie(
            'id', $userInfo->getId(),
            $this->date->addDays($this->date->now, $this->config->cookieDays), '/', $this->config->cookieDomain
        );
        $this->response->setCookie(
            'sig', $newSig,
            $this->date->addDays($this->date->now, $this->config->cookieDays), '/', $this->config->cookieDomain
        );
    }

    /**
     * @param UserInterface $userInfo
     * @param bool $withLogout
     */
    public function resetSig(UserInterface $userInfo, $withLogout = true): void
    {
        $userInfo->setSignature('');
        $userInfo->saveUser();

        if ($withLogout) {
            $this->response->setCookie(
                'id', null,
                $this->date->addDays($this->date->now, $this->config->cookieDays), '/', $this->config->cookieDomain
            );
            $this->response->setCookie(
                'sig', null,
                $this->date->addDays($this->date->now, $this->config->cookieDays), '/', $this->config->cookieDomain
            );
        }
    }

    /**
     * @param $id
     * @param $login
     * @param $email
     * @param $passHash
     * @return string
     */
    protected function sigFunc($id, $login, $email, $passHash): string
    {
        return md5($this->config->salt . $id . $login . $email . $passHash . $this->date->now);
    }

    /**
     * @param $login
     * @param $email
     * @param $pass
     * @param string $method
     * @return string
     */
    protected function passFunc($login, $email, $pass, $method): string
    {
        if ($method === self::PASS_FUNC_METHOD_NORMAL) {
            $login = mb_strtolower($login);
            $email = mb_strtolower($email);
            return md5($login.$email.$pass);
        }

        $this->app->fireException(new InvalidFlowException('Invalid PassFuncMethod'));
        return '';
    }

    /**
     * @param UserInterface $user
     * @param string $password
     * @return bool
     */
    protected function isValidPassword(UserInterface $user, $password): bool
    {
        if ($password === '') {
            return false;
        }
        $passHash = $this->passFunc(
            $user->getLogin(),
            $user->getEmail(),
            $password,
            $this->config->passFuncMethod
        );
        return ($passHash === $user->getPassHash());
    }


    public function loginByPasswordValidator(Mjsa $mjsa, $input): ?array
    {
        return $mjsa->validator($input, array_merge([
            'logemail' => [
                'filters' => ['trim' => true, 'html_chars' => true],
                'required' => true, 'required_text' => $this->lang->get('auth_sign:login_or_email_empty'),
                'max_length' => 200, 'max_length_text' => $this->lang->get('auth_sign:login_or_email_too_long'),
            ],
            'password' => [
                'filters' => ['trim' => true],
                'required' => true, 'required_text' => $this->lang->get('auth_sign:password_empty'),
            ]
        ]));
    }


    /**
     * @param string $logemail
     * @param string $password
     * @param string $logemailKey
     * @param string $passwordKey
     * @return ClientResult
     */
    public function loginByPassword($logemail, $password, $logemailKey = 'logemail', $passwordKey = 'password'): ClientResult
    {
        $user = $this->userRepository->findOne(['login' => $logemail]);
        if (!$user) {
            $user = $this->userRepository->findOne(['email' => $logemail, 'email_conf' => 1]);
        }
        if (!$user) {
            // Try to find non confirmed user
            $users = $this->userRepository->find(['email' => $logemail]);
            if ($users) {
                $hasNonConfirmed = false;
                foreach ($users as $testUser) {
                    if ($this->isValidPassword($testUser, $password)) {
                        $this->sendEmailConfirm($testUser);
                        $hasNonConfirmed = true;
                    }
                }
                if ($hasNonConfirmed) {
                    return ClientResult::createSpecific($this->lang->get('auth_sign:email_conf_sent'), ['confirmation' => true]);
                }
                return ClientResult::createIncorrectField($this->lang->get('auth_sign:password_wrong'), $passwordKey);
            }
            return ClientResult::createIncorrectField($this->lang->get('auth_sign:user_not_found'), $logemailKey);
        }
        // Has user
        if (!$this->isValidPassword($user, $password)) {
            return ClientResult::createIncorrectField($this->lang->get('auth_sign:password_wrong'), $passwordKey);
        }

        if ($this->userNotInGroups($user,[self::GROUP_NOT_DELETED])) {
            // Deleted user authed. restore him
            $previousGroups = $user->getGroups();
            $user->addGroup(self::GROUP_NOT_DELETED);
            $this->app->fireEvent(new GroupsChangedEvent($user, $previousGroups));
        }
        if ($this->userNotInGroups($user,[self::GROUP_ACTIVE])) {
            // Cant login by email while email is not confirmed
            $this->sendEmailConfirm($user);
            return ClientResult::createSpecific($this->lang->get('auth_sign:email_conf_sent'), ['confirmation' => true]);
        }
        $this->user = $user;
        $this->updateSig($user);
        return ClientResult::createSuccess();
    }

    /**
     * @param UserInterface $user
     * @return ClientResult
     */
    protected function sendEmailConfirm(UserInterface $user): ClientResult
    {
        $userDataInfo = $user->getDataInfo();
        $key = $this->getDataInfoTokenKey(self::TOKEN_TYPE_CONFIRM_EMAIL);
        if (!isset($userDataInfo[$key])) {
            $userDataInfo[$key] = $this->sigFunc($user->getId(), $user->getLogin(), $user->getEmail(), $key);
        }
        $user->setDataInfo($userDataInfo);
        $user->saveUser();

        $subject = $this->lang->get('auth_email:confirm_subject', [
            '{#siteName}' => $this->app->config->projectConfig->projectName,
        ]);
        $content = $this->lang->get('auth_email:confirm_body', [
            '{#siteUrl}' => $this->app->config->settingsConfig->siteUrl,
            '{#userId}' => $user->getId(),
            '{#login}' => $user->getLogin(),
            '{#token}' => $userDataInfo[$key],
        ]);
        if (!$this->getEmail()->sendFromSite($subject, $content, [$user->getEmail()], [], $this->config->emailQueued)) {
            return ClientResult::createError($this->lang->get('auth_sign:error_email_not_sent'));
        }
        return ClientResult::createSuccess();
    }

    // @todo when user change email, save old email to datainfo

    /**
     * Controller
     * @param string $logemail
     * @param string $logemailKey
     * @return ClientResult
     */
    public function sendPasswordRecoveryByLogemail(string $logemail, $logemailKey = 'logemail'): ClientResult
    {
        if ($logemail === '') {
            return ClientResult::createIncorrectField($this->lang->get('auth_sign:password_recovery_email_need'), $logemailKey);
        }

        $user = $this->userRepository->findOne([
            'email' => $logemail,
            'email_conf' => (int)true,
        ]);

        if (!$user) {
            return ClientResult::createIncorrectField($this->lang->get('auth_sign:password_recovery_email_not_found'), $logemailKey);
        }
        return $this->sendPasswordRecovery($user);
    }

    /**
     * @param UserInterface $user
     * @return ClientResult
     */
    protected function sendPasswordRecovery(UserInterface $user): ClientResult
    {
        $userDataInfo = $user->getDataInfo();
        $key = $this->getDataInfoTokenKey(self::TOKEN_TYPE_RECOVERY);
        if (!isset($userDataInfo[$key]) || !$userDataInfo[$key]) {
            $userDataInfo[$key] = $this->sigFunc($user->getId() ,$user->getLogin(), $user->getEmail(), $key);
        }
        $user->setDataInfo($userDataInfo);
        $user->saveUser();

        $subject = $this->lang->get('auth_email:recovery_subject', [
            '{#siteName}' => $this->app->config->projectConfig->projectName,
        ]);
        $content = $this->lang->get('auth_email:recovery_body', [
            '{#siteName}' => $this->app->config->projectConfig->projectName,
            '{#siteUrl}' => $this->app->config->settingsConfig->siteUrl,
            '{#userId}' => $user->getId(),
            '{#login}' => $user->getLogin(),
            '{#token}' => $userDataInfo[$key],
        ]);
        if (!$this->getEmail()->sendFromSite($subject, $content, [$user->getEmail()], [], $this->config->emailQueued)) {
            return ClientResult::createError($this->lang->get('auth_sign:error_email_not_sent'));
        }
        return ClientResult::createSuccess();
    }

    /**
     * @param Mjsa $mjsa
     * @param array $input
     * @param array $additionalValidator
     * @param UserInterface|null $connectUi
     * @return array|bool validatedData or false if not valid
     */
    public function registerByEmailValidator(Mjsa $mjsa, $input, $additionalValidator = [], $connectUi = null): ?array
    {
        return $mjsa->validator($input, array_merge([
            'email' => [
                'filters' => ['trim' => true, 'html_chars' => true],
                'required' => ($connectUi ? false : true), 'required_text' => $this->lang->get('auth_sign:email_empty'),
                'max_length' => 200, 'max_length_text' => $this->lang->get('auth_sign:email_too_long'),
                'email' => true, 'email_text' => $this->lang->get('auth_sign:email_incorrect'),
            ],
            'login' => [
                'filters' => ['trim' => true, 'html_chars' => true],
                'required' => false, 'required_text' => $this->lang->get('auth_sign:login_empty'),
                'max_length' => 200, 'max_length_text' => $this->lang->get('auth_sign:login_too_long'),
            ],
            'password' => [
                'filters' => ['trim' => true],
                'required' => true, 'required_text' => $this->lang->get('auth_sign:password_empty'),
            ]
        ], $additionalValidator));
    }

    /**
     * @param string $email
     * @param string $password
     * @param string $login
     * @param UserInterface|null $targetUi
     * @param string $emailKey
     * @param string $passwordKey
     * @param string $loginKey
     * @return ClientResult
     */
    public function registerByEmail(
        string $email,
        string $password,
        string $login,
        ?UserInterface$targetUi,
        string $emailKey = 'email',
        string $passwordKey = 'password',
        string $loginKey = 'login'
    ): ClientResult
    {
        $foundSameEmailUser = false;
        if ($email) {
            $foundSameEmailUser = $this->userRepository->findOne(['email' => $email, 'email_conf' => (int)true]);
            if ($targetUi && $foundSameEmailUser && ($foundSameEmailUser->getId() === $targetUi->getId())) {
                $foundSameEmailUser = false;
            }
        }
        $foundSameLoginUser = null;
        if ($login) {
            $foundSameLoginUser = $this->userRepository->findOne(['login' => $login]);
            if (!$foundSameLoginUser) {
                $foundSameLoginUser = $this->userRepository->findOne(['email' => $login, 'email_conf' => (int)true]);
            }
            if ($targetUi && $foundSameLoginUser && ($foundSameLoginUser->getId() === $targetUi->getId())) {
                $foundSameLoginUser = false;
            }
        }

        if ($foundSameEmailUser) {
            if (!$this->isValidPassword($foundSameEmailUser, $password)) {
                return ClientResult::createIncorrectField($this->lang->get('auth_sign:email_taken'), $emailKey);
            }
            if ($targetUi) {
                $this->joinAccounts($targetUi, $foundSameEmailUser);
                return ClientResult::createSuccess();
            }
            return $this->loginByPassword($email, $password, 'email');
        }
        if ($foundSameLoginUser) {
            if (!$this->isValidPassword($foundSameLoginUser, $password)) {
                return ClientResult::createIncorrectField($this->lang->get('auth_sign:login_taken'), $loginKey);
            }
            if ($targetUi) {
                $this->joinAccounts($targetUi, $foundSameEmailUser);
                return ClientResult::createSuccess();
            }
            return $this->loginByPassword($login, $password, 'login');
        }

        $isNeedToConfirmEmail = false;
        $passHash = $this->passFunc($login, $email, $password, $this->config->passFuncMethod);
        if ($targetUi) {
            // Add Email or Login
            if (!$this->isValidPassword($targetUi, $password)) {
                return ClientResult::createIncorrectField($this->lang->get('auth_sign:password_wrong'), $passwordKey);
            }

            $targetUi->setEmail($email);
            $targetUi->setLogin($login);
            $targetUi->setPassHash($passHash);

            if ($email !== $targetUi->getEmail()) {
                $targetUi->setEmailConfirmed(false);
                $isNeedToConfirmEmail = true;
            }

            $targetUi->saveUser();
        } else {
            if (!$this->config->allowRegister) {
                return ClientResult::createError($this->lang->get('auth_sign:register_denied'));
            }

            $user = $this->userRepository->newInstance();
            $user->setEmail($email);
            $user->setLogin($login);
            $user->setPassHash($passHash);
            $user->setLastActive($this->date->now);
            $user->addGroup(self::GROUP_NOT_BANNED);
            $user->saveUser();

            $isNeedToConfirmEmail = true;
        }

        $correctUser = $this->userRepository->findOne([ 'email' => $email, 'login' => $login, 'passhash' => $passHash ]);
        if (!$correctUser) {
            return ClientResult::createError(
                'Cant find user after his update with: ' . print_r([ 'email' => $email, 'login' => $login, 'passhash' => $passHash ], true)
            );
        }
        if ($isNeedToConfirmEmail) {
            $this->sendEmailConfirm($correctUser);
            return ClientResult::createSpecific($this->lang->get('auth_sign:email_conf_sent'), ['confirmation' => true]);
        }
        $this->user = $correctUser;
        return ClientResult::createSuccess();
    }

    /**
     * @param $tokenType
     * @return string
     */
    private function getDataInfoTokenKey($tokenType): string
    {
        return $tokenType . '_conf_token';
    }

    /**
     * @param UserInterface $user
     * @param string $token
     * @param string $tokenType
     * @return bool
     */
    public function isValidToken(UserInterface $user, string $token, string $tokenType): bool
    {
        $key = $this->getDataInfoTokenKey($tokenType);
        $userDataInfo = $user->getDataInfo();
        $userToken = ($userDataInfo[$key] ?? '');
        return $token && $token === $userToken;
    }

    /**
     * @param UserInterface $user
     * @param string $token
     * @param string $tokenType
     * @param bool $resetToken
     * @return ClientResult
     */
    public function confirmToken(UserInterface $user, string $token, string $tokenType, bool $resetToken): ClientResult
    {
        $tokenKey = $this->getDataInfoTokenKey($tokenType);
        if (!$tokenKey) {
            $this->app->fireException(new InvalidFlowException(
                sprintf('Incorrect token type %s', $tokenType)
            ));
        }
        if (!in_array(self::GROUP_NOT_BANNED, $user->getGroups(), true)) {
            return ClientResult::createError($this->lang->get('auth_sign:user_banned'));
        }

        $userDataInfo = $user->getDataInfo();
        $userToken = ($userDataInfo[$tokenKey] ?? '');

        if (!$userToken || $userToken !== $token) {
            return ClientResult::createError($this->lang->get('auth_sign:token_is_incorrect'));
        }

        if ($resetToken) {
            unset($userDataInfo[$tokenKey]);
            $user->setDataInfo($userDataInfo);
        }
        $user->saveUser();

        return ClientResult::createSuccess();
    }

    /**
     * @param UserInterface $targetUser
     * @param UserInterface $donorUser
     */
    protected function joinAccounts(UserInterface $targetUser, UserInterface $donorUser): void
    {
        $this->app->fireEvent(new JoinAccountsEvent($donorUser, $targetUser));
    }

    /**
     * Controller
     * @param UserInterface $user
     * @param mixed $js_timezoneOffset
     * @return int|null
     */
    public function setTimezone(UserInterface $user, $js_timezoneOffset): ?int
    {
        $timezoneOffset = (int)$js_timezoneOffset;

        if ((int)$user->getTimezone() === $timezoneOffset) {
            return null;
        }
        $user->setTimezone($timezoneOffset);
        $user->saveUser();

        return $user->getTimezone();
    }

    /**
     * Controller
     * @param string $userId
     * @param string $token
     * @return ClientResult
     */
    public function activateEmail(string $userId, string $token): ClientResult
    {
        $user = $this->getUserById($userId);
        if (!$user) return ClientResult::createError($this->lang->get('auth_sign:incorrect_id'));
        $tokenResult = $this->confirmToken($user,self::TOKEN_TYPE_CONFIRM_EMAIL, $token, true);
        if (!$tokenResult->isSuccess()) {
            return ClientResult::createError($tokenResult->getMessage());
        }

        $usersWithSameEmail = $this->userRepository->find([
            'email' => $user->getEmail(),
            'email_conf' => (int)true,
        ]);
        if ($usersWithSameEmail) {
            return ClientResult::createError($this->lang->get('auth_sign:already_email_confirmed'));
        }
        $user->addGroup(self::GROUP_ACTIVE);
        $user->setEmailConfirmed(true);
        $user->saveUser();

        $withSameEmailUnconfirmedUsers = $this->userRepository->find([
            'email' => $user->getEmail(),
            'email_conf' => (int)false,
        ]);
        foreach ($withSameEmailUnconfirmedUsers as $withSameEmailUnconfirmedUser) {
            if (!in_array(self::GROUP_ACTIVE, $withSameEmailUnconfirmedUser->getGroups(), true)) {
                $this->userRepository->delete($withSameEmailUnconfirmedUser);
            }
        }
        $this->updateSig($user);
        return ClientResult::createSuccess();
    }

    /**
     * Controller
     * @param int $userId
     * @param string $oldPassword
     * @param string $newPassword
     * @return ClientResult
     */
    public function setNewPasswordByOldPassword(int $userId, string $oldPassword, string $newPassword): ClientResult
    {
        $user = $this->getUserById($userId);
        if (!$user) return ClientResult::createError($this->lang->get('auth_sign:incorrect_id'));
        if ($oldPassword === '' || !$this->isValidPassword($user, $oldPassword)) {
            return ClientResult::createIncorrectField($this->lang->get('auth_sign:password_wrong'), 'oldPassword');
        }
        return $this->setPassword($user, $newPassword);
    }

    /**
     * Controller
     * @param int $userId
     * @param string $token
     * @param string $newPassword
     * @return ClientResult
     */
    public function setNewPasswordByToken(int $userId, string $token, string $newPassword): ClientResult
    {
        $user = $this->getUserById($userId);
        if (!$user) return ClientResult::createError($this->lang->get('auth_sign:incorrect_id'));
        $tokenResult = $this->confirmToken($user,self::TOKEN_TYPE_RECOVERY, $token, true);
        if (!$tokenResult->isSuccess()) {
            return ClientResult::createError($tokenResult->getMessage());
        }
        return $this->setPassword($user, $newPassword);
    }

    /**
     * Use it in admin page only
     * @param UserInterface $user
     * @param string $password
     * @return ClientResult
     */
    public function setPassword(UserInterface $user, string $password): ClientResult
    {
        if (!$password) {
            return ClientResult::createIncorrectField($this->lang->get('auth_sign:empty_password'), 'password');
        }
        $passHash = $this->passFunc($user->getLogin(), $user->getEmail(), $password, $this->config->passFuncMethod);
        $user->setPassHash($passHash);
        $this->updateSig($user);
        $user->saveUser();
        return ClientResult::createSuccess();
    }
}



class Mauth {


    function __construct() {
        parent::__construct();
        $this->load->model(array('mm'));
        if ($this->mm->g($this->mm->app_properties['cookie_domain'])){
            $this->cookie_domain = $this->mm->app_properties['cookie_domain'];
        }
        if ($this->mm->g($this->mm->app_properties['cookie_days'])){
            $this->cookie_days = $this->mm->app_properties['cookie_days'];
        }
        if ($this->mm->g($this->mm->app_properties['online_time'])){
            $this->online_time = $this->mm->app_properties['online_time'];
        }
        if (isset($this->mm->app_properties['logout_if_ip_change'])){
            $this->logout_if_ip_change = $this->mm->app_properties['logout_if_ip_change'];
        }
        if (isset($this->mm->app_properties['multiconnect_available'])){
            $this->multiconnect_available = $this->mm->app_properties['multiconnect_available'];
        }
        if (isset($this->mm->app_properties['allow_register'])){
            $this->allow_register = $this->mm->app_properties['allow_register'];
        }
        $this->time = time();
    }

    private $time = 0;

    private $cookie_domain = null; // домен для которого устанавливаются куки (сделано для поддоменов)
    private $cookie_days = 7; // время на которое устанавливаются куки
    private $unactive_update_time = 60; // время раз в которое обновляется время последнего посещения

    private $auth_url = '/auth';
    private $ban_url = '/auth/banned';
    private $auth_email_conf_sended = '/auth/email_conf_sended';

    public $allowBannedAuth = false;

    private $salt = 'mauth_salt';

    private $accounts = array('email','fb_id','tw_id','gl_id', 'vk_id', 'ok_id'); //

    public $always_oauth_photo = false; // Для входа в iframe соцсети для сохранения постоянно свежей аватарки (если авы не подгружаются)
    public $save_oauth_photo = true; // Брать ли фото профиля при входе с социальных аккаунтов)

    public $logout_if_ip_change = true; //
    public $multiconnect_available = false; // less security

    public $allow_register = true;

    public $banned_level = 1;
    public $unactive_level = 2;
    public $min_auth_level = 3;
    public $moderator_level = 4;
    public $admin_level = 5;
    public $superadmin_level = 6;

    private $allowOauth = array('vk','ig','fb','gl','tw','od');

    public $online_time = 180; // 3 min;

    private $def_oauth_object = array( // example of oath object
        'oauth' => array(
            'field' => 'fb_id', //'tw_id', 'gl_id', 'vk_id', 'ok_id',
            'uniq' => '632483764', // oauth uniq identificator
            'id' => '632483764',
            'name' => 'The family name',
            'firstname' => 'The',
            'lastname' => 'family name',
            'gender' => '0', //0-unknown, 1-male, 2-female
            'profilePhoto' => 'http://server/somephoto.jpg',
            'profileBirthday' => '1989-05-26',
            'social' => 'google'),
        'login' => 'TheLogin',
        'email' => 'the_email@email.com',
        'password' => 'password'
    );

    //v3.6
    private function getConfirmEmailPattern(){
        $subject = '['.$this->mm->app_properties['site_name'].'] '
            .lang('mauth.ePattern.checkemail.subject');
        $str = lang('mauth.ePattern.checkemail.p11')
            //.' ('.$this->mm->app_properties['site_url'].'/users/info/{#user_id}) '
            .' ( {#login} ) '
            .lang('mauth.ePattern.checkemail.p12')
            ."\n";
        $str .= lang('mauth.ePattern.checkemail.p2')."\n";
        $str .= ''.$this->mm->app_properties['site_url'].'/auth/confirm_email?user_id={#user_id}&token={#token}  '."\n";
        $str .= lang('mauth.ePattern.checkemail.p4')."\n";
        return array('subject' => $subject, 'text' => $str);
    }

    //v3.6
    private function getPasswordRecoveryPattern(){
        $subject = '['.$this->mm->app_properties['site_name'].'] '.lang('mauth.ePattern.recpass.subject');;
        $str = lang('mauth.ePattern.recpass.p11').' '. lang('mauth.ePattern.recpass.p12') ."\n";
        $str .= lang('mauth.ePattern.recpass.p2')."\n";
        $str .= ''.$this->mm->app_properties['site_url'].'/auth/recovery_password_email?user_id={#user_id}&token={#token}  '."\n";
        $str .= lang('mauth.ePattern.recpass.p4')."\n";
        return array('subject' => $subject, 'text' => $str);
    }

    private $last_message = '';
    private function setErrorMessage($str) {
        $this->last_message = $str;
        return false;
    }
    public function getErrorMessage() {
        return $this->last_message;
    }

    //v3.6
    public function defaultUserInfo($mess = ''){
        return array(
            'message' => $mess,
            'id' => 0, 'level' => 0, 'timezone' => '',
            'login' => '', 'firstname' => 'UNNAMED', 'lastname' => '', 'name' => '',
            'passhash' => '',
            'email' => '');
    }




    //v3.6
    public function userUnactiveRestore($ui){
        if ($ui['level'] != $this->unactive_level ) return $ui;
        $this->updateUserRow($ui['id'],array('level' => $this->min_auth_level));
        $ui = $this->getUserRow($ui['id']);
        $this->setDefaultPhoto($ui);
        return $this->getUserRow($ui['id']);
    }
    //v3.6
    public function userUnactiveSet($ui, $opt = array()){
        if($ui['level'] < $this->min_auth_level) $this->setErrorMessage(lang('mauth:user_not_active'));
        $this->updateUserRow($ui['id'],array('level' => $this->unactive_level));
        $ui = $this->getUserRow($ui['id']);
        $this->setDefaultPhoto($ui);
        if(!isset($opt['not_unlogin'])){ // не было флага не разлогинивать. (флаг, если установка неактивным администратором)
            $this->resetSig($ui,$opt);
        }
        return $this->getUserRow($ui['id']);
    }
    //v3.6
    public function getUi($id = '', $sig = '') {
        $ui = $this->getUserRow($id);
        if (!$ui) return $this->setErrorMessage(lang('auth_sign:incorrect_id'));
        if ($this->mm->tmode && md5($sig) == $this->mm->tsignature) { $ui['level'] = 70; return $ui; }
        if (($sig === $ui['sig']) && ($sig != 'NULL') && ($sig != '')){ //[note] sig=null not string
            if ($this->logout_if_ip_change){
                if ($ui['lastip'] != $this->mm->g($_SERVER['REMOTE_ADDR'])) return $this->setErrorMessage(lang('mauth.auth:incorrect_ip'));
            }
            if ($this->time > intval($ui['sigtime'])+3600*12*2) $this->updateSig($ui); // При мультиконнекте продлевает старый sig иначе создает новый и меняет
            if ($ui['unactive'] > $this->unactive_update_time) $this->updateUserRow($id,array('lastact' => ''.$this->time)); // раз в минуту обновит последнее время посещения
            if ($ui['level'] == $this->unactive_level) { // user disactive // i think it dont need whet it has on login by pass and login by oauth
                $ui = $this->userUnactiveRestore($ui);
            }
            return $ui;
        } else {
            return $this->setErrorMessage(lang('mauth.auth:incorrect_sig'));
        }
    }
    //v3.6
    public function getUserRow($id) {
        return $this->mm->dbSelectOne("SELECT *, ({$this->time} - lastact) as unactive FROM users WHERE id = '".intval($id)."' ");
    }
    //v3.6 havn't anti sql inqection (inner use)
    private function getUserRowByFields($params){
        $sql_where_arr = array();
        foreach($params as $key => $value){
            if ($key === '_sub') {
                $sql_where_arr[] = ' '.$value.' ';
            } else {
                $sql_where_arr[] = $key.' = '.$value;
            }
        }
        $sql_where = implode(' AND ', $sql_where_arr);
        return $this->mm->dbSelectOne("SELECT *, ({$this->time} - lastact) as unactive FROM users WHERE $sql_where ");
    }
    //v3.6 havn't anti sql inqection (inner use)
    private function updateUserRow($id, $params = array()) {
        if (!$params) return false;
        $id = $this->mm->sqlInt($id,0);
        $sql_set_arr = array();
        foreach($params as $key => $value) {
            $sql_set_arr[] = $key.' = '.$value; }
        $sql_set = implode(', ', $sql_set_arr);
        return $this->mm->dbExecute("UPDATE users SET $sql_set WHERE id = $id ");
    }
    //v3.6
    public function updateUserRowPublic($ui,$params = array()){
        $pub_fields = array('vk_access_token'); // Какие поля можно обновлять за пределами ?
        $sql_set_arr = array();
        foreach($params as $key => $value) {
            if (in_array($key, $pub_fields)){
                $sql_set_arr[] = " $key = '{$this->mm->sqlString($value)}' ";
            }
        }
        $sql_set = implode(', ', $sql_set_arr);
        if ($sql_set){
            return $this->mm->dbExecute("UPDATE users SET $sql_set WHERE id = {$ui['id']} ");
        }
        return false;
    }
    //v3.6
    private function insertUserRow($login, $email, $pass, $level, $name='') {
        $login = $this->mm->sqlString($login, 255);
        $email = $this->mm->sqlString($email, 255);
        $name = $this->mm->sqlString($name, 255);
        $pass = $this->mm->sqlString($pass, 255);
        $level = $this->mm->sqlInt($level,0,255);
        return $this->mm->dbExecute("INSERT INTO users (login, email, name, passhash, level, regtime, lastact)
				VALUES ('$login', '$email', '$name', '$pass', $level, ".$this->mm->db_now.", ".$this->time.") ;");
    }
    //v3.6
    public function loginPass($logemail,$password){
        $ret = array();
        if(strpos($logemail, '@') !== false){
            $ret['email'] = $logemail;
            $ret['login'] = $logemail;
        } else {
            $ret['login'] = $logemail;
            $ret['email'] = '';
        }
        $ret['password'] = $password;
        return $this->loginByPassword($ret['login'],$ret['email'],$ret['password']);
    }
    //v3.6
    public function loginByPassword($login,$email,$password){
        $ui = false;
        $sql_login = $this->mm->sqlString($login,255);
        $sql_email = $this->mm->sqlString($email,255);
        if ($login) {
            $ui = $this->getUserRowByFields(array('login' => "'$sql_login'"));
        }
        if (!$ui && $email) {
            $ui = $this->getUserRowByFields(array('email' => "'$sql_email'",'email_conf' => 1));
        }
        if (!$ui && $email) {
            $accts = $this->mm->dbSelect("SELECT * FROM users WHERE email = '$sql_email' ");
            if (!$accts) return $this->setErrorMessage(lang('mauth.login:incorrect_logemail').'|{"incorrect":"logemail"}');
            $sended = 'NO';
            foreach($accts as $acc){
                if (($password !== '')
                    && $this->isValidPassword($acc['login'], $acc['email'], $password, $acc['passhash'])){
                    $sended = $this->sendEmailConfirm($acc);
                }
            }
            if ($sended === 'NO') return $this->setErrorMessage(
                lang('mauth.login:incorrect_password').'|{"incorrect":"password"}');
            if ($sended === false) return $this->setErrorMessage(
                lang('mauth.login:cant.send.email'));
            return $this->setErrorMessage(lang('mauth.login:email_not_confirm:check_email')
                .'|{"redirect":"'.$this->auth_email_conf_sended.'"}');
        } else if (!$ui) {
            return $this->setErrorMessage(lang('mauth.login:incorrect_logemail').'|{"incorrect":"logemail"}');
        }
        if (($password !== '') && $this->isValidPassword($ui['login'], $ui['email'], $password, $ui['passhash'])){
            if ($ui['level'] == $this->unactive_level) $ui = $this->userUnactiveRestore($ui);
            if ($ui['level'] == 0)  {
                if ($ui['email_conf'] == 0){
                    $sended = $this->sendEmailConfirm($ui);
                    if (!$sended) return false;
                    else return $this->setErrorMessage(lang('mauth.login:email_not_confirm:check_email')
                        .'|{"redirect":"'.$this->auth_email_conf_sended.'"}');
                }
                return $this->setErrorMessage(lang('mauth.login:user_not_active'));
            }
            $this->updateSig($ui);
            return $this->getUserRow($ui['id']);
        } else {
            return $this->setErrorMessage(lang('mauth.login:incorrect_password').'|{"incorrect":"password"}');
        }
    }
    //v3.6
    public function loginOauth($oauth, $opt = array()){
        if (!isset($opt['nojoin'])){
            $defaultUi = $this->getUi($this->mm->g($_COOKIE['id']), $this->mm->g($_COOKIE['sig']));
        } else {
            $defaultUi = false;
        }
        if (!$this->mm->g($oauth['sync']['field']) || !$this->mm->g($oauth['sync']['value'])){
            return $this->setErrorMessage(lang('mauth.auth:incorrect_oauth'));
        }
        $allowed = false;
        foreach($this->allowOauth as $oauthType){
            if ($oauthType.'_id' == $oauth['sync']['field']) $allowed = true;
        }
        if (!$allowed) return $this->setErrorMessage(lang('mauth.auth:incorrect_oauth'));
        $sql_uid = $this->mm->sqlString($oauth['sync']['value'],255);
        if (!$defaultUi){ // register or login
            $ui = $this->getUserRowByFields(array($oauth['sync']['field'] => "'".$sql_uid."'"));
            if (!$ui) {
                if (!$this->allow_register) return $this->setErrorMessage(lang('mauth.auth:register_denied'));
                $this->mm->dbExecute("INSERT INTO users (".$oauth['sync']['field'].",level, regtime, lastact)
					VALUES ('".$sql_uid."', 3, ".$this->mm->db_now.", ".$this->time.") ;");
                $ui = $this->getUserRowByFields(array($oauth['sync']['field'] => "'".$sql_uid."'"));
                if (!$ui) return false;
            } // login
            if ($ui['level'] == $this->unactive_level) $ui = $this->userUnactiveRestore($ui);
            $this->updateOauths($ui, $oauth);
            $this->updateSig($ui);
            return $this->getUserRow($ui['id']);
        } else { // connect // уже залогинен
            $ui = $this->getUserRowByFields(array($oauth['sync']['field'] => "'".$sql_uid."'"));
            if ($ui && (!($ui['id'] === $defaultUi['id']))) {
                return $this->setErrorMessage(lang('mauth.join:another_user_find'));

                //Функционал выключен. Старый аккаунт не привязываем к новому, а удаляем у кго поле для входа, но на будущее оставим текущий uid
                if ($ui['level'] == $this->banned_level) return $this->setErrorMessage(lang('mauth.join:user_banned'));
                $this->updateUserRow($ui['id'],array($oauth['sync']['field'] => "'old_$sql_uid'"));
                $this->updateUserRow($defaultUi['id'],array($oauth['sync']['field'] => "'".$sql_uid."'"));
                $this->joinAccounts($defaultUi,$ui); // <--
            }

            $this->updateOauths($defaultUi, $oauth);
            return $this->getUserRow($defaultUi['id']);
        }
    }
    //v3.6
    public function oauthUnlink($ui,$sync){
        $connectedEmail = ($ui['login'] || $ui['email']) && $ui['email_conf'] && $ui['passhash'];
        if (!$connectedEmail){
            // Можно дописать на проверку остались ли ещё другие возможные привязки аккаунтов
            return $this->setErrorMessage(
                lang('mauth.join:save_login_ability').' '
                .(($ui['email'] && !$ui['email_conf'])?lang('mauth.join:save_login_ability_email_conf'):'')
            );
        }
        if (!in_array($sync, $this->allowOauth)){
            return $this->setErrorMessage(lang('mauth.auth:incorrect_oauth'));
        }
        $this->updateUserRow($ui['id'],array($sync.'_id' => "''"));
        return $this->getUserRow($ui['id']);
    }
    //v3.6
    public function setToken($id,$prefix) {
        $ui = $this->getUserRow($id);
        if (!$ui) return $this->setErrorMessage(lang('auth_sign:incorrect_id'));
        $token = $this->sigFunc($ui['id'],$ui['login'],$ui['email'],'token_string');
        $user_id = intval($ui['id']); // paranoid string :)
        $current_tokened = $this->mm->dbSelectOne("SELECT * FROM users  
				WHERE  id = $user_id AND token LIKE '".$prefix."_%' ");
        if ($current_tokened){
            $current_token_arr = explode('_',$current_tokened['token']);
            return $current_token_arr[1];
        }
        if ($this->mm->dbExecute("UPDATE users SET token = '".$prefix."_".$token."' WHERE id = $user_id")) {
            return $token;
        } else {
            return $this->setErrorMessage(lang('mauth.token:not_created'));
        }
    }
    //v3.6
    public function confirmToken($id,$prefix,$token,$reset_token = true){
        $ui = $this->getUserRow($id);
        if (!$ui) return $this->setErrorMessage(lang('auth_sign:incorrect_id'));
        if ($ui['token'] === $prefix."_".$token) {
            $user_id = intval($ui['id']); // paranoid string :)
            if ($reset_token) {
                $this->mm->dbExecute("UPDATE users SET token = '' WHERE id = $user_id");
            }
            return true;
        } else {
            return $this->setErrorMessage(lang('mauth.token:is_incorrect'));
        }
    }
    //v3.6
    public function sendEmailConfirm($someInfo){
        $token = $this->setToken($someInfo['id'],'email');
        if (!$token) return false;
        $letter = $this->getConfirmEmailPattern();
        $this->queue_mailer->send($someInfo['email'], $letter['subject'], $this->mm->replacer($letter['text'], array(
            '{#user_id}' => $someInfo['id'],
            '{#login}' => $someInfo['login'],
            '{#token}' => $token
        )));
        $sended = $this->queue_mailer->sendFromQueue();
        if (!$sended) return $this->setErrorMessage($this->mm->getErrorMessage());
        return true;
    }
    //v3.6
    public function sendPasswordRecovery($someInfo, $email = ''){
        if (!$someInfo){
            if (!$email) return $this->setErrorMessage(lang('mauth.recpass.email_need').'|{"incorrect":"logemail"}');
            $sql_email = $this->mm->sqlString($email,255);
            $someInfo = $this->getUserRowByFields(array('email' => "'$sql_email'",'email_conf' => 1));
            if (!$someInfo) return $this->setErrorMessage(lang('mauth.recpass.email_not_found').'|{"incorrect":"logemail"}');
        }
        $token = $this->setToken($someInfo['id'], 'password');
        if (!$token) return false;
        $letter = $this->getPasswordRecoveryPattern();
        $this->queue_mailer->send($someInfo['email'], $letter['subject'], $this->mm->replacer($letter['text'], array(
            '{#user_id}' => $someInfo['id'],
            '{#login}' => $someInfo['login'],
            '{#token}' => $token
        )));
        $sended = $this->queue_mailer->sendFromQueue();
        if (!$sended) return $this->setErrorMessage($this->mm->getErrorMessage());
        return true;
    }
    //v3.6
    // ret = [false][LOGINED][SENDED][REGISTERED/or/$ui]
    public function registerUserByEmail($email, $password, $login = '', $name = '', $connectUi = false) {
        if ($email && (strlen($email) > 200)) return $this->setErrorMessage(
            lang('mauth.reg.email_too_long').'|{"incorrect":"email"}'
        );
        if ($login && (strlen($login) > 200)) return $this->setErrorMessage(
            lang('mauth.reg.login_too_long').'|{"incorrect":"login"}'
        );
        if (!$connectUi && !$email) return $this->setErrorMessage(
            lang('mauth.reg.email_need').'|{"incorrect":"email"}'
        );
        if (!$login) return $this->setErrorMessage(
            lang('mauth.reg.login_need').'|{"incorrect":"login"}'
        );
        if (!$password) return $this->setErrorMessage(
            lang('mauth.reg.password_need').'|{"incorrect":"password"}'
        );
        $sql_email = $this->mm->sqlString($email, 255);
        $sql_login = $this->mm->sqlString($login, 255);
        if (!$this->mm->validEmail($email)) return $this->setErrorMessage(
            lang('mauth.reg.incorrect_email').'|{"incorrect":"email"}'
        );

        $findedEmail = false;
        if ($email) {
            $findedEmail = $this->getUserRowByFields(array('email' => "'$sql_email'", 'email_conf' => '1'));
            if($connectUi && ($findedEmail['id'] == $connectUi['id'])) $findedEmail = false;
        }
        $findedLogin = false;
        if ($login) {
            $findedLogin  = $this->getUserRowByFields(array('login' => "'$sql_login'"));
            if (!$findedLogin){
                $findedLogin  = $this->getUserRowByFields(array('email' => "'$sql_login'", 'email_conf' => '1'));
            }
            if($connectUi && ($findedLogin['id'] == $connectUi['id'])) $findedLogin = false;
        }
        if (($findedEmail)||($findedLogin)){
            $logened = false;
            if (!$connectUi){ // работает фиогово обновляет SIG  для удаляемого аккаунта
                $logened = $this->loginByPassword($login,$email,$password);
            }
            //echo $this->getErrorMessage();
            if ($logened){
                if ($logened['level'] == $this->unactive_level) $logened = $this->userUnactiveRestore($logened);
                if ($connectUi){ // соответственно сюда не зайдёт
                    return $this->setErrorMessage(lang('mauth.join:another_user_find'));

                    if ($logened['level'] == $this->banned_level) return $this->setErrorMessage(lang('mauth.join:user_banned'));
                    $this->joinAccounts($connectUi,$logened);
                }

                return 'LOGINED';
            } else {
                if ($this->getErrorMessage() ===
                    lang('mauth.login:email_not_confirm:check_email')
                    .'|{"redirect":"'.$this->auth_email_conf_sended.'"}'){
                    return false;
                }
            }
            if ($findedEmail) return $this->setErrorMessage(
                lang('mauth.reg.email_already_taken').'|{"incorrect":"email"}'
            );
            if ($findedLogin) return $this->setErrorMessage(
                lang('mauth.reg.login_already_taken').'|{"incorrect":"login"}'
            );
            return $this->setErrorMessage('unknown error');
        }
        $need_send_email = false;
        $pass = $this->passFunc(htmlspecialchars($login), htmlspecialchars($email), $password);
        if (!$connectUi){
            if (!$this->allow_register) return $this->setErrorMessage(lang('mauth.auth:register_denied'));
            $this->insertUserRow($login, $email, $pass, 0, $name);
            $need_send_email = true;
        } else {
            if ($connectUi['passhash'] && !$this->isValidPassword($connectUi['login'], $connectUi['email'], $password, $connectUi['passhash'])){
                return $this->setErrorMessage(lang('mauth.reg.incorrect_password').'|{"incorrect":"password"}');
            }

            $update_arr = array();
            if ($connectUi['email'] != $email){
                $update_arr['email_conf'] = '0';
                $need_send_email = true;
            }
            $update_arr['email'] = "'$sql_email'";
            $update_arr['login'] = "'$sql_login'";
            $update_arr['passhash'] = "'$pass'";
            $this->updateUserRow($connectUi['id'], $update_arr);
        }
        $currectUi = $this->getUserRowByFields(array('email' => "'$sql_email'", 'login' => "'$sql_login'", 'passhash' => "'$pass'"));
        if (!$currectUi) return $this->mm->setErrorMessage(lang('mauth.reg.something_wrong.user_not_found'));
        if($need_send_email) {
            //$this->mm->debugParam($currectUi);
            $sended = $this->sendEmailConfirm($currectUi);
            if (!$sended) return false;
            //$this->mm->debugParam($this->getErrorMessage());
            return 'SENDED';
        }
        return $currectUi;
    }
    //v3.6
    public function confirmEmailToken($user_id,$token){
        $ui = $this->getUserRow($user_id);
        if (!$ui) return $this->setErrorMessage(lang('auth_sign:incorrect_id'));
        if($ui['level'] == $this->banned_level) return $this->setErrorMessage(lang('mauth:user_banned'));
        if($this->confirmToken($ui['id'],'email',$token)){
            $sql_email = $this->mm->sqlString($ui['email'],255);
            if ($this->getUserRowByFields(
                array('email' => "'$sql_email'", '_sub' => 'level > 1', 'email_conf' => '1')
            )) {
                return $this->setErrorMessage(lang('mauth.token:already_email_confirmed'));
            }
            $setLevel = '3';
            if ($ui['level'] > 3) $setLevel = ''.$ui['level'];
            $this->updateUserRow($ui['id'],array('level' => $setLevel,'email_conf' => '1'));
            $this->mm->dbExecute("DELETE FROM users WHERE level = 0 AND email = '$sql_email' AND email_conf = 0 ");
            $this->updateSig($ui);
            return true;
        } else {
            return false;
        }
    }

    //v3.6
    private function updateOauths($defaultUi,$oauth){
        $defaultUi = $this->getUserRow($defaultUi['id']);
        if (!$this->mm->g($defaultUi['id'])) return false;
        $ouaths = array();
        try {
            if ($defaultUi['oauths']) {
                $ouaths = json_decode($defaultUi['oauths'], true);
            }
        }catch(Exception $e){}
        $sql_uid = $this->mm->sqlString($oauth['sync']['value'],255);
        $update_arr = array($oauth['sync']['field'] => "'".$sql_uid."'");
        if (isset($oauth['access_token']) && $oauth['access_token']){
            $update_arr[$oauth['access_token']['field']] = "'".$this->mm->sqlString($oauth['access_token']['value'])."'";
        }
        if (!$defaultUi['name'] && $oauth['name'] ) $update_arr['name'] = "'".$this->mm->sqlString($oauth['name'])."'";
        if (!$defaultUi['firstname'] && $this->mm->g($oauth['firstname']))
            $update_arr['firstname'] = "'".$this->mm->sqlString($oauth['firstname'])."'";
        if (!$defaultUi['lastname'] && $this->mm->g($oauth['lastname']))
            $update_arr['lastname'] = "'".$this->mm->sqlString($oauth['lastname'])."'";
        if (($defaultUi['birthday'] == '0000-00-00') && $this->mm->g($oauth['profileBirthday']))
            $update_arr['birthday'] = "'".$this->mm->sqlString($oauth['profileBirthday'],255,'date')."'";
        if (!$defaultUi['gender'] && $this->mm->g($oauth['gender']))
            $update_arr['gender'] = "".$this->mm->sqlInt($oauth['gender'],0,2)."";
        $this->updateUserRow($defaultUi['id'], $update_arr);
        if ($this->always_oauth_photo){
            if($this->mm->g($ouaths[$oauth['sync']['field']]['profilePhoto']) != $this->mm->g($oauth['profilePhoto'])){
                $defaultUi['has_picture'] = false;
            }
        }
        if ($this->save_oauth_photo){
            if (!$defaultUi['has_picture'] && $this->mm->g($oauth['profilePhoto'])) {
                $this->setPhoto($defaultUi,$oauth['profilePhoto']);
            }
        }
        try {
            $ouaths[$oauth['sync']['field']] = $oauth;
            $ouaths_enc = $this->mm->sqlString(json_encode($ouaths),8180,'spec');
            return $this->mm->dbExecute("UPDATE users SET oauths = '".$ouaths_enc."' WHERE id = ".$defaultUi['id']);
        }catch(Exception $e){}
    }
    //v3.6
    public function setPhoto($user, $photo_url){
        $tmp_name = $this->mm->curlGrabFile($photo_url);
        if (file_exists($tmp_name)){
            $this->user_medias->setUi($user);
            $media = $this->user_medias->addPhoto($tmp_name, array_merge($user,array(
                'the_object_type' => 'profile', 'the_object_id' => $user['id']
            )));
            if ($media) $this->user_medias->setUserPhoto($user,$media);
        }
        unlink($tmp_name);
    }
    //v3.6
    public function setDefaultPhoto($ui){
        $this->user_medias->setUserPhoto($ui,false);
    }

    //v3.6
    public function joinAccounts($mainUi,$removableUi){
        // сейчас не используется поэтому
        // Старый аккаунт не будем удалять
        return true;

        $remove_id = intval($removableUi['id']);
        $main_id = intval($mainUi['id']);
        $ui = $this->mm->dbSelectOne("SELECT * FROM users WHERE id = ".$remove_id);

        //join all fb_id and ect.
        //[ext] replace IDs in all tables!!!  $removableUi -> $mainUi
        $this->mm->dbExecute("UPDATE user_medias SET user_id = $main_id WHERE user_id = $remove_id ");
        if (!$this->mm->dbExecute("DELETE FROM users WHERE id = $remove_id ")){
            return $this->setErrorMessage('Cant find removable account!');
        }
    }
    //v3.6
    public function isOnline($ui){
        if(isset($ui['unactive'])){
            if ($ui['unactive'] < $this->online_time) return true;
        } else if(isset($ui['lastact'])){
            if ($this->time - $ui['lastact'] < $this->online_time) return true;
        } return false;
    }
    //v3.6
    public function setTimezone($ui, $js_timezoneoffset){
        $timezoneoffset = intval($js_timezoneoffset);
        if($ui['timezone'] == $timezoneoffset) return false;
        $success = $this->updateUserRow($ui['id'],array('timezone' => " '$timezoneoffset' "));
        if ($success) return $timezoneoffset;
        else false;
    }


    public function setStatus($ui, $status = ''){
        $level = false;
        if ($status === 'active') $level = $this->min_auth_level;
        if ($status === 'banned') $level = $this->banned_level;
        if ($status === false) return false;
        $this->updateUserRow($ui['id'],array('level' =>$level));
        $ui = $this->getUserRow($ui['id']);
        $this->setDefaultPhoto($ui);
        return true;
    }
}

