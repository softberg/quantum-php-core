<?php

namespace Quantum\Tests\Unit\Libraries\Auth\Adapters;

use Quantum\Libraries\Auth\Adapters\SessionAuthAdapter;
use Quantum\Libraries\Auth\Exceptions\AuthException;
use Quantum\Tests\Unit\Libraries\Auth\AuthTestCase;
use Quantum\Libraries\Hasher\Hasher;
use Quantum\Libraries\Auth\User;

class SessionAuthAdapterTest extends AuthTestCase
{

    private $sessionAuth;

    public function setUp(): void
    {
        parent::setUp();

        config()->set('auth.two_fa', false);

        $this->sessionAuth = new SessionAuthAdapter($this->authService, $this->mailer, new Hasher);

        $admin = $this->sessionAuth->signup($this->adminUser);

        $this->sessionAuth->activate($admin->getFieldValue('activation_token'));
    }

    public function tearDown(): void
    {
        self::$users = [];

        $this->sessionAuth->signout();
    }

    public function testWebAdapterConstructor()
    {
        $this->assertInstanceOf(SessionAuthAdapter::class, $this->sessionAuth);
    }

    public function testWebSigninIncorrectCredentials()
    {
        $this->expectException(AuthException::class);

        $this->expectExceptionMessage('incorrect_auth_credentials');

        $this->sessionAuth->signin('admin@qt.com', '111111');
    }

    public function testWebSigninCorrectCredentials()
    {
        $this->assertTrue($this->sessionAuth->signin('admin@qt.com', 'qwerty'));

        $this->assertTrue($this->sessionAuth->signin('admin@qt.com', 'qwerty', true));
    }

    public function testWebSigninWithRemember()
    {
        $this->sessionAuth->signin('admin@qt.com', 'qwerty', true);

        session()->delete('auth_user');

        $this->assertTrue($this->sessionAuth->check());
    }

    public function testWebSignout()
    {
        $this->assertFalse($this->sessionAuth->check());

        $this->sessionAuth->signin('admin@qt.com', 'qwerty');

        $this->assertTrue($this->sessionAuth->check());

        $this->sessionAuth->signout();

        $this->assertFalse($this->sessionAuth->check());
    }

    public function testWebUser()
    {
        $this->sessionAuth->signin('admin@qt.com', 'qwerty');

        $this->assertInstanceOf(User::class, $this->sessionAuth->user());

        $this->assertEquals('admin@qt.com', $this->sessionAuth->user()->getFieldValue('email'));

        $this->assertEquals('admin', $this->sessionAuth->user()->getFieldValue('role'));
    }

    public function testWebCheck()
    {
        $this->assertFalse($this->sessionAuth->check());

        $this->sessionAuth->signin('admin@qt.com', 'qwerty');

        $this->assertTrue($this->sessionAuth->check());
    }

    public function testWebSignupAndSigninWithoutActivation()
    {

        $this->expectException(AuthException::class);

        $this->expectExceptionMessage('inactive_account');

        $this->sessionAuth->signup($this->guestUser);

        $this->assertTrue($this->sessionAuth->signin('guest@qt.com', '123456'));
    }

    public function testWebSignupAndActivateAccount()
    {
        $user = $this->sessionAuth->signup($this->guestUser);

        $this->sessionAuth->activate($user->getFieldValue('activation_token'));

        $this->assertTrue($this->sessionAuth->signin('guest@qt.com', '123456'));
    }

    public function testWebForgetReset()
    {
        $resetToken = $this->sessionAuth->forget('admin@qt.com');

        $this->sessionAuth->reset($resetToken, '123456789');

        $this->assertTrue($this->sessionAuth->signin('admin@qt.com', '123456789'));
    }

    public function testWebVerifyOtp()
    {
        config()->set('auth.two_fa', true);

        config()->set('auth.otp_expires', 2);

        $otp_token = $this->sessionAuth->signin('admin@qt.com', 'qwerty');

        $this->assertTrue($this->sessionAuth->verifyOtp(123456789, $otp_token));
    }

    public function testWebSigninWithoutVerification()
    {
        config()->set('auth.two_fa', false);

        config()->set('auth.otp_expires', 2);

        $this->assertTrue($this->sessionAuth->signin('admin@qt.com', 'qwerty'));
    }

    public function testWebSigninWithVerification()
    {
        config()->set('auth.two_fa', true);

        config()->set('auth.otp_expires', 2);

        $this->assertIsString($this->sessionAuth->signin('admin@qt.com', 'qwerty'));
    }

    public function testWebResendOtp()
    {
        config()->set('auth.two_fa', true);

        config()->set('auth.otp_expires', 2);

        $otp_token = $this->sessionAuth->signin('admin@qt.com', 'qwerty');

        $this->assertIsString($this->sessionAuth->resendOtp($otp_token));
    }

    public function testWebRefreshUser()
    {
        $this->sessionAuth->signin('admin@qt.com', 'qwerty');

        $user = $this->sessionAuth->user();

        $this->assertEquals('Admin', $user->firstname);

        $this->assertEquals('User', $user->lastname);

        $newUserData = [
            'firstname' => 'Super',
            'lastname' => 'Human',
        ];

        $this->authService->update('uuid', $user->uuid, $newUserData);

        $this->sessionAuth->refreshUser($user->uuid);

        $refreshedUser = $this->sessionAuth->user();

        $this->assertEquals('Super', $refreshedUser->firstname);

        $this->assertEquals('Human', $refreshedUser->lastname);
    }
}