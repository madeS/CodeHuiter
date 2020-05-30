<?php

namespace CodeHuiter\FunctionalTest\Cases\Auth;

use CodeHuiter\Core\Application;
use CodeHuiter\Service\ByDefault\Email\Model\Mailer;
use CodeHuiter\Service\Language;
use CodeHuiter\FunctionalTest\Base\FakeRequest\BaseFakeRequestApplicationTestCase;
use CodeHuiter\Service\RelationalRepositoryProvider;

class AuthTest extends BaseFakeRequestApplicationTestCase
{
    public function testUnitTestExecution(): void
    {
        self::assertEquals(true, true);
    }

    public function testFailRegister(): void
    {
        $lang = self::getLang();

        // No Email
        $response = self::runWithPost($this, '/auth/register_submit?jsonAjax=1', []);
        self::assertHasErrorMessage($lang->get('auth_sign:email_empty'), $response);
        self::assertHasIncorrectInputs('email', $response);

        // Invalid Email
        $response = self::runWithPost($this, '/auth/register_submit?jsonAjax=1', ['email' => 'unit_test_example.com']);
        self::assertHasErrorMessage($lang->get('auth_sign:email_incorrect'), $response);
        self::assertHasIncorrectInputs('email', $response);

        // No Password
        $response = self::runWithPost($this, '/auth/register_submit?jsonAjax=1', ['email' => 'test_success_user@example.com']);
        self::assertHasErrorMessage($lang->get('auth_sign:password_empty'), $response);
        self::assertHasIncorrectInputs('password', $response);
    }

    public function testSuccessRegister(): void
    {
        self::userRegisterByEmail($this, 'test_user_success@example.com', 'test_success_user_login', 'testUser1Pa22word');
        self::userRegisterByEmail($this, 'test_user_success2@example.com', '', 'testUser2Pa22word');
    }

    public function testFailTokenConfirmation(): void
    {
        [$userId, $token] = self::userRegisterForToken($this, 'test_user_for_token@example.com', '', 'testUserPass');

        // Confirmation with incorrect User
        $response = self::runWithGet($this, "/auth/confirm_email?user_id=99999&token={$token}&jsonAjax=1");
        self::assertEquals(403, self::getHeaderCode($response));

        // Confirmation with incorrect Token
        $response = self::runWithGet($this, "/auth/confirm_email?user_id={$userId}&token=IncorrectToken&jsonAjax=1");
        self::assertEquals(403, self::getHeaderCode($response));
    }

    public function testSuccessLogin(): void
    {
        $successLoginEmail = 'testEmail@example.com';
        $successLoginLogin = 'testLogin';
        $successPassword = 'passWord123';
        self::userRegisterByEmail($this, $successLoginEmail, $successLoginLogin, $successPassword);

        self::userLogin($this, $successLoginEmail, $successPassword);
        self::userLogin($this, $successLoginLogin, $successPassword);
        self::userLogin($this, strtolower($successLoginEmail), $successPassword);
        self::userLogin($this, strtoupper($successLoginEmail), $successPassword);
        self::userLogin($this, strtolower($successLoginLogin), $successPassword);
        self::userLogin($this, strtoupper($successLoginLogin), $successPassword);
    }

    public function testFailLogin(): void
    {
        $lang = self::getLang();
        self::userRegisterByEmail($this, 'test_user_for_fail_login@example.com', 'test_user_for_fail_login', 'testPa22Word');

        // No Logemail
        $response = self::runWithPost($this, '/auth/login_submit?jsonAjax=1', ['logemail' => '', 'password' => '']);
        self::assertHasErrorMessage($lang->get('auth_sign:login_or_email_empty'), $response);
        self::assertHasIncorrectInputs('logemail', $response);

        // No Password
        $response = self::runWithPost($this, '/auth/login_submit?jsonAjax=1', ['logemail' => 'some_login', 'password' => '']);
        self::assertHasErrorMessage($lang->get('auth_sign:password_empty'), $response);
        self::assertHasIncorrectInputs('password', $response);

        // Incorrect Logemail
        $response = self::runWithPost($this, '/auth/login_submit?jsonAjax=1', ['logemail' => 'not_existed', 'password' => 'some_password']);
        self::assertHasErrorMessage($lang->get('auth_sign:user_not_found'), $response);
        self::assertHasIncorrectInputs('logemail', $response);

        // Wrong Password with CorrectEmeil
        $response = self::runWithPost($this, '/auth/login_submit?jsonAjax=1', ['logemail' => 'test_user_for_fail_login@example.com', 'password' => 'testPa22word']);
        self::assertHasErrorMessage($lang->get('auth_sign:password_wrong'), $response);
        self::assertHasIncorrectInputs('password', $response);

        // Wrong Password with Login
        $response = self::runWithPost($this, '/auth/login_submit?jsonAjax=1', ['logemail' => 'test_user_for_fail_login', 'password' => 'testPa22word']);
        self::assertHasErrorMessage($lang->get('auth_sign:password_wrong'), $response);
        self::assertHasIncorrectInputs('password', $response);
    }

    public function testLoginViaRegisterFormForUnconfirmedUser(): void
    {
        // Login via register for unconfirmed users
        [$userId1, $token1] = self::userRegisterForToken($this, 'test_user_for_login_via_register_unconfirmed@example.com', '', 'somePassWord');
        [$userId2, $token2] = self::userRegisterForToken($this, 'test_user_for_login_via_register_unconfirmed@example.com', '', 'somePassWord');

        self::assertEquals($userId1, $userId2);
        self::assertEquals($token1, $token2);
    }

    public function testLoginViaRegisterForm(): void
    {
        $email = 'test_user_for_login_via_register@example.com';
        $login = 'test_user_for_login_via_register_login';
        $password = 'somePassWord22';
        self::userRegisterByEmail($this, $email, $login, $password);

        // Success with full equals
        $response = self::runWithPost($this, '/auth/register_submit?jsonAjax=1', ['email' => $email, 'login' => $login, 'password' => $password]);
        $cookies = self::getCookies($response);
        self::assertArrayHasKey('id', $cookies, 'No success login with with full equal registration');
        self::assertArrayHasKey('sig', $cookies, 'No success login with with full equal registration');

        // Success with email equals
        $response = self::runWithPost($this, '/auth/register_submit?jsonAjax=1', ['email' => $email, 'login' => 'IncorrectLogin', 'password' => $password]);
        $cookies = self::getCookies($response);
        self::assertArrayHasKey('id', $cookies, 'No success login with with email equal registration');
        self::assertArrayHasKey('sig', $cookies, 'No success login with with email equal registration');

        // Success with login equals
        $response = self::runWithPost($this, '/auth/register_submit?jsonAjax=1', ['email' => 'incorrectEmail@example.com', 'login' => $login, 'password' => $password]);
        $cookies = self::getCookies($response);
        self::assertArrayHasKey('id', $cookies, 'No success login with with login equal registration');
        self::assertArrayHasKey('sig', $cookies, 'No success login with with login equal registration');
    }

    public function testRegisterWithTakenUniqueData(): void
    {
        $lang = self::getLang();
        $existEmail = 'existEmail@example.com';
        $existLogin = 'existLogin';
        $existPassword = 'existPassword';
        self::userRegisterByEmail($this, $existEmail, $existLogin, $existPassword);

        // Email Taken
        $response = self::runWithPost($this, '/auth/register_submit?jsonAjax=1', ['email' => $existEmail, 'login' => 'NewLogin', 'password' => 'newPassword']);
        self::assertHasErrorMessage($lang->get('auth_sign:email_taken'), $response);
        self::assertHasIncorrectInputs('email', $response);

        // Login Taken
        $response = self::runWithPost($this, '/auth/register_submit?jsonAjax=1', ['email' => 'newEmail@example.com', 'login' => $existLogin, 'password' => 'newPassword']);
        self::assertHasErrorMessage($lang->get('auth_sign:login_taken'), $response);
        self::assertHasIncorrectInputs('login', $response);

        // Success login
        $response = self::runWithPost($this, '/auth/register_submit?jsonAjax=1', ['email' => $existEmail, 'login' => $existLogin, 'password' => $existPassword]);
        $cookies = self::getCookies($response);
        self::assertArrayHasKey('id', $cookies, 'No success login with with equal registration');
        self::assertArrayHasKey('sig', $cookies, 'No success login with with equal registration');
    }

    public function testLoginUnConfirmUser(): void
    {
        $lang = self::getLang();
        $existUnconfirmedEmail = 'existUnconfirmedEmail@example.com';
        $existUnconfirmedLogin = 'existUnconfirmedLogin';
        $existUnconfirmedPassword = 'existUnconfirmedPassword';
        self::userRegisterForToken($this, $existUnconfirmedEmail, $existUnconfirmedLogin, $existUnconfirmedPassword);

        // Login via unconfirmed email
        $response = self::runWithPost($this, '/auth/login_submit?jsonAjax=1', ['logemail' => $existUnconfirmedEmail, 'password' => $existUnconfirmedPassword]);
        self::assertStringContainsString($lang->get('auth_sign:email_conf_sent'), self::getJsonAjaxFormReplaceContent($response));

        // Login via unconfirmed login
        $response = self::runWithPost($this, '/auth/login_submit?jsonAjax=1', ['logemail' => $existUnconfirmedLogin, 'password' => $existUnconfirmedPassword]);
        self::assertStringContainsString($lang->get('auth_sign:email_conf_sent'), self::getJsonAjaxFormReplaceContent($response));
    }

    public function testAnotherRegisterWithUnconfirmedUserAndSecondTokenSuccess(): void
    {
        $lang = self::getLang();
        $firstUnconfirmedEmail = 'firstUnconfirmedEmail@example.com';
        $firstUnconfirmedLogin = 'firstUnconfirmedLogin';
        $firstUnconfirmedPassword = 'firstUnconfirmedPassword';
        self::userRegisterForToken($this, $firstUnconfirmedEmail, $firstUnconfirmedLogin, $firstUnconfirmedPassword);

        // Register with same login
        $response = self::runWithPost($this, '/auth/register_submit?jsonAjax=1', ['email' => 'secondUnconfirmedEmail@example.com', 'login' => $firstUnconfirmedLogin, 'password' => 'secondUnconfirmedPassword']);
        self::assertHasErrorMessage($lang->get('auth_sign:login_taken'), $response);
        self::assertHasIncorrectInputs('login', $response);

        // Register with same email
        $response = self::runWithPost($this, '/auth/register_submit?jsonAjax=1', ['email' => $firstUnconfirmedEmail, 'login' => 'secondUnconfirmedLogin', 'password' => 'secondUnconfirmedPassword']);
        self::assertStringContainsString($lang->get('auth_sign:email_conf_sent'), self::getJsonAjaxFormReplaceContent($response));

        // Confirm second account
        [$userId2, $token2] = self::getUserTokenFromMailer($firstUnconfirmedEmail);
        $response = self::runWithGet($this, "/auth/confirm_email?user_id={$userId2}&token={$token2}&jsonAjax=1");
        self::assertEquals('/users/settings', self::getHeaderLocation($response));

        // Login with first password
        $response = self::runWithPost($this, '/auth/login_submit?jsonAjax=1', ['logemail' => $firstUnconfirmedEmail, 'password' => $firstUnconfirmedPassword]);
        self::assertHasErrorMessage($lang->get('auth_sign:password_wrong'), $response);
        self::assertHasIncorrectInputs('password', $response);

        // Login with second Password
        $response = self::runWithPost($this, '/auth/login_submit?jsonAjax=1', ['logemail' => $firstUnconfirmedEmail, 'password' => 'secondUnconfirmedPassword']);
        $cookies = self::getCookies($response);
        self::assertArrayHasKey('id', $cookies, 'No success login with second registration');
        self::assertArrayHasKey('sig', $cookies, 'No success login with second registration');
    }

    public function testAnotherRegisterWithUnconfirmedUserAndFirstTokenSuccess(): void
    {
        $lang = self::getLang();
        $firstUnconfirmedEmail = 'firstUnconfirmedEmailer@example.com';
        $firstUnconfirmedLogin = 'firstUnconfirmedLoginer';
        $firstUnconfirmedPassword = 'firstUnconfirmedPassword';
        self::userRegisterForToken($this, $firstUnconfirmedEmail, $firstUnconfirmedLogin, $firstUnconfirmedPassword);
        [$userId1, $token1] = self::getUserTokenFromMailer($firstUnconfirmedEmail);

        // Register with same login
        $response = self::runWithPost($this, '/auth/register_submit?jsonAjax=1', ['email' => 'secondUnconfirmedEmailer@example.com', 'login' => $firstUnconfirmedLogin, 'password' => 'secondUnconfirmedPassword']);
        self::assertHasErrorMessage($lang->get('auth_sign:login_taken'), $response);
        self::assertHasIncorrectInputs('login', $response);

        // Register with same email
        $response = self::runWithPost($this, '/auth/register_submit?jsonAjax=1', ['email' => $firstUnconfirmedEmail, 'login' => 'secondUnconfirmedLoginer', 'password' => 'secondUnconfirmedPassword']);
        self::assertStringContainsString($lang->get('auth_sign:email_conf_sent'), self::getJsonAjaxFormReplaceContent($response));

        // Confirm first account
        [$userId2, $token2] = self::getUserTokenFromMailer($firstUnconfirmedEmail);
        self::assertNotEquals($userId1, $userId2);
        self::assertNotEquals($token1, $token2);

        $response = self::runWithGet($this, "/auth/confirm_email?user_id={$userId1}&token={$token1}&jsonAjax=1");
        self::assertEquals('/users/settings', self::getHeaderLocation($response));

        // Login with second password
        $response = self::runWithPost($this, '/auth/login_submit?jsonAjax=1', ['logemail' => $firstUnconfirmedEmail, 'password' => 'secondUnconfirmedPassword']);
        self::assertHasErrorMessage($lang->get('auth_sign:password_wrong'), $response);
        self::assertHasIncorrectInputs('password', $response);

        // Login with first Password
        $response = self::runWithPost($this, '/auth/login_submit?jsonAjax=1', ['logemail' => $firstUnconfirmedEmail, 'password' => $firstUnconfirmedPassword]);
        $cookies = self::getCookies($response);
        self::assertArrayHasKey('id', $cookies, 'No success login with second registration');
        self::assertArrayHasKey('sig', $cookies, 'No success login with second registration');
    }

    public function testRecoveryPassword(): void
    {
        $lang = self::getLang();
        $email = 'testRecoveryUserByToken@example.com';
        $login = 'testRecoveryUserByTokenLogin';
        $firstPassword = 'firstPassword';
        $secondPassword = 'secondPassword';
        self::userRegisterByEmail($this, $email, $login, $firstPassword);

        // Incorrect email
        $response = self::runWithPost($this, '/auth/password_recovery_submit?jsonAjax=1', ['logemail' => 'invalidTestRecoveryUserByToken@example.com', 'password' => '']);
        self::assertHasErrorMessage($lang->get('auth_sign:password_recovery_email_not_found'), $response);
        self::assertHasIncorrectInputs('logemail', $response);

        // Send token
        $response = self::runWithPost($this, '/auth/password_recovery_submit?jsonAjax=1', ['logemail' => $email, 'password' => '']);
        self::assertStringContainsString($lang->get('auth_sign:recovery_link_sent'), self::getJsonAjaxFormReplaceContent($response));

        // Recovery front error
        $response = self::runWithGet($this, '/auth/recovery');
        self::assertEquals(403, self::getHeaderCode($response));

        [$userId, $token] = self::getUserTokenFromMailer($email);

        // Recovery front success
        $response = self::runWithGet($this, "/auth/recovery?user_id=$userId&token=$token");
        self::assertResponseWithoutError($response);

        // Change password by token
        $response = self::runWithPost($this, '/auth/user_edit_password_submit?jsonAjax=1', ['user_id' => $userId, 'token' => $token, 'newpassword' => $secondPassword, 'newpassword_conf' => $secondPassword]);
        self::assertHasSuccessMessage($lang->get('auth_sign:user_info_changed'), $response);

        self::userLogin($this, $email, $secondPassword);
    }

    public function testFrontPages(): void
    {
        $response = self::runWithGet($this, '/auth');
        self::assertResponseWithoutError($response);
    }

    public static function loginWithNewUser(BaseFakeRequestApplicationTestCase $those, string $email, string $login, string $password): array
    {
        self::userRegisterByEmail($those, $email, $login, $password);
        return self::userLogin($those, $email, $password);
    }

    public static function userLogin(BaseFakeRequestApplicationTestCase $those, string $loginOrEmail, string $password): array
    {
        $response = self::runWithPost($those, '/auth/login_submit?jsonAjax=1', ['logemail' => $loginOrEmail, 'password' => $password]);
        $cookies = self::getCookies($response);
        self::assertArrayHasKey('id', $cookies);
        self::assertArrayHasKey('sig', $cookies);
        return $cookies;
    }

    public static function userRegisterByEmail(BaseFakeRequestApplicationTestCase $those, string $email, string $login, string $password): array
    {
        [$userId, $token] = self::userRegisterForToken($those, $email, $login, $password);
        // Confirmation ok with Autologin
        $response = self::runWithGet($those, "/auth/confirm_email?user_id={$userId}&token={$token}&jsonAjax=1");
        self::assertEquals('/users/settings', self::getHeaderLocation($response));
        $cookies = self::getCookies($response);
        self::assertArrayHasKey('id', $cookies);
        self::assertArrayHasKey('sig', $cookies);
        return [$cookies['id'], $cookies['sig']];
    }

    protected static function userRegisterForToken(BaseFakeRequestApplicationTestCase $those, string $email, string $login, string $password): array
    {
        $lang = self::getApplication()->get(Language::class);
        $response = self::runWithPost($those, '/auth/register_submit?jsonAjax=1', ['email' => $email, 'login' => $login, 'password' => $password]);
        $replaceContent = self::getJsonAjaxFormReplaceContent($response);
        if ($replaceContent === null) {
            self::fail('already registered');
        }
        self::assertStringContainsString($lang->get('auth_sign:email_conf_sent'), self::getJsonAjaxFormReplaceContent($response));
        [$userId, $token] = self::getUserTokenFromMailer($email);
        return [$userId, $token];
    }

    public static function getUserTokenFromMailer(string $email): array
    {
        $application = Application::getInstance();
        /** @var RelationalRepositoryProvider $repositoryProvider */
        $repositoryProvider = $application->get(RelationalRepositoryProvider::class);
        $mailerRepository = $repositoryProvider->get(Mailer::class);

        /** @var Mailer $mailer */
        $mailer = $mailerRepository->findOne(
            ['email' => $email],
            ['order' => ['id' => 'desc']]
        );
        self::assertNotEmpty($mailer, 'Mailer for registered user1 not found');
        preg_match('/token=([^\s\n&]+)/i',$mailer->message, $matches);
        $token = $matches[1] ?? null;
        self::assertNotEmpty($token, "Token not found in message [$mailer->message]");
        preg_match('/user_id=([^\s\n&]+)/i',$mailer->message, $matches);
        $userId = $matches[1] ?? null;
        self::assertNotEmpty($userId, "UserId not found in message [$mailer->message]");
        return [$userId, $token];
    }
}
