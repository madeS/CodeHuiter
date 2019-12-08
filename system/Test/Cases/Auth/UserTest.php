<?php

namespace CodeHuiter\Test\Cases\Auth;

use CodeHuiter\Test\Base\FakeRequest\BaseFakeRequestApplicationTestCase;

class UserTest extends BaseFakeRequestApplicationTestCase
{
    public function testPublicFrontPages(): void
    {
        $response = self::runWithGet($this, '/users');
        self::assertResponseWithoutError($response);

        $response = self::runWithGet($this, '/users/id50');
        self::assertResponseWithoutError($response);
    }

    public function testSignInFrontPages(): void
    {
        $cookieData = AuthTest::loginWithNewUser($this, 'signInFrontPageUser@example.com', 'signInFrontPageUser', 'password');

        $response = self::runWithGet($this, '/users', $cookieData);
        self::assertResponseWithoutError($response);

        $response = self::runWithGet($this, '/users/id50', $cookieData);
        self::assertResponseWithoutError($response);

        $response = self::runWithGet($this, '/users/settings', $cookieData);
        self::assertResponseWithoutError($response);
    }

    public function testUserSettingsSave(): void
    {
        $cookieData = AuthTest::loginWithNewUser($this, 'UserSettingsSave@example.com', 'UserSettingsSave', 'password');

        $response = self::runWithGet($this, '/users/settings', $cookieData);
        self::assertResponseWithoutError($response);

        $loadedParser = self::getParser()->load($response->getContent());
        $name = $loadedParser->findOne('h1')->content();
        self::assertEquals('UserSettingsSave', $name, '/user/settings has invalid H1 name');

        // First Edit
        $response = self::runWithPost($this, '/users/user_edit_submit', [
            'name' => 'MyName1',
            'city' => 'New York',
            'about_me' => 'SomeWords about me',
        ], $cookieData);
        self::assertResponseWithoutError($response);

        $response = self::runWithGet($this, '/users/settings', $cookieData);
        self::assertResponseWithoutError($response);

        $loadedParser = self::getParser()->load($response->getContent());
        $name = $loadedParser->findOne('h1')->content();
        self::assertEquals('MyName1', $name, '/user/settings has invalid H1 name');
        $city = $loadedParser->findOne('input[name=city]')->attr('value');
        self::assertEquals('New York', $city, '/user/settings has invalid input[name=city] city');
        $aboutMe = $loadedParser->findOne('textarea[name=about_me]')->content();
        self::assertEquals('SomeWords about me', $aboutMe, '/user/settings has invalid textarea[name=about_me] about_me');

        // Second Edit
        $response = self::runWithPost($this, '/users/user_edit_submit', [
            'birthday_year' => '2007',
            'birthday_month' => '2',
            'birthday_day' => '31',
        ], $cookieData);
        self::assertResponseWithoutError($response);

        $response = self::runWithGet($this, '/users/settings', $cookieData);
        self::assertResponseWithoutError($response);

        $loadedParser = self::getParser()->load($response->getContent());
        $name = $loadedParser->findOne('h1')->content();
        self::assertEquals('MyName1', $name, '/user/settings has invalid H1 name');
    }

    public function testChangeLoginAndEmail(): void
    {
        $lang = self::getLang();
        $cookieData = AuthTest::loginWithNewUser($this, 'UserChangeLoginAndEmail@example.com', 'UserChangeLoginAndEmail', 'password');

        // Change with wrong password
        $response = self::runWithPost($this, '/auth/user_edit_logemail_submit', [
            'login' => 'UserChangeLoginAndEmail2',
            'email' => 'UserChangeLoginAndEmail@example.com',
            'password' => 'password2',
        ], $cookieData);
        self::assertResponseWithoutError($response);
        self::assertHasIncorrectInputs('password', $response);

        // Change login
        $response = self::runWithPost($this, '/auth/user_edit_logemail_submit', [
            'login' => 'UserChangeLoginAndEmail2',
            'email' => 'UserChangeLoginAndEmail@example.com',
            'password' => 'password',
        ], $cookieData);
        self::assertResponseWithoutError($response);

        $response = self::runWithGet($this, '/users/settings', $cookieData);
        self::assertResponseWithoutError($response);
        $loadedParser = self::getParser()->load($response->getContent());
        $login = $loadedParser->findOne('input[name=login]')->attr('value');
        self::assertEquals('UserChangeLoginAndEmail2', $login, '/user/settings has invalid input[name=login] login');

        // Change email
        $response = self::runWithPost($this, '/auth/user_edit_logemail_submit', [
            'login' => 'UserChangeLoginAndEmail1',
            'email' => 'UserChangeLoginAndEmail2@example.com',
            'password' => 'password',
        ], $cookieData);
        self::assertResponseWithoutError($response);
        self::assertStringContainsString($lang->get('auth_sign:email_conf_sent'), self::getJsonAjaxFormReplaceContent($response));

        // Confirm first account
        [$userId, $token] = AuthTest::getUserTokenFromMailer('UserChangeLoginAndEmail2@example.com');
        $response = self::runWithGet($this, "/auth/confirm_email?user_id={$userId}&token={$token}&jsonAjax=1");
        $cookieData = self::getCookies($response);
        self::assertEquals('/users/settings', self::getHeaderLocation($response));

        $response = self::runWithGet($this, '/users/settings', $cookieData);
        self::assertResponseWithoutError($response);
        $loadedParser = self::getParser()->load($response->getContent());
        $login = $loadedParser->findOne('input[name=login]')->attr('value');
        $email = $loadedParser->findOne('input[name=email]')->attr('value');
        self::assertEquals('UserChangeLoginAndEmail1', $login, '/user/settings has invalid input[name=login] login');
        self::assertEquals('UserChangeLoginAndEmail2@example.com', $email, '/user/settings has invalid input[name=login] login');
    }

    public function testChangePassword(): void
    {
        $lang = self::getLang();
        $cookieData = AuthTest::loginWithNewUser($this, 'UserChangePassword@example.com', 'UserChangePassword', 'password1');

        // Change login
        $response = self::runWithPost($this, '/auth/user_edit_password_submit', [
            'password' => 'password1',
            'newpassword' => 'password2',
            'newpassword_conf' => 'password2',
        ], $cookieData);
        self::assertResponseWithoutError($response);

        AuthTest::userLogin($this, 'UserChangePassword', 'password2');
    }
}
