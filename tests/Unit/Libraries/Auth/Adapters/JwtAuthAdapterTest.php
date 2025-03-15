<?php

namespace Quantum\Tests\Unit\Libraries\Auth\Adapters;

use Quantum\Libraries\Auth\Adapters\SessionAuthAdapter;
use Quantum\Libraries\Auth\Exceptions\AuthException;
use Quantum\Tests\Unit\Libraries\Auth\AuthTestCase;
use Quantum\Libraries\Hasher\Hasher;
use Quantum\Libraries\Auth\User;

class JwtAuthAdapterTest extends AuthTestCase
{

    private $webAuth;

    public function setUp(): void
    {
        parent::setUp();

        $this->webAuth = new SessionAuthAdapter($this->authService, $this->mailer, new Hasher);

        $admin = $this->webAuth->signup($this->adminUser);

        $this->webAuth->activate($admin->getFieldValue('activation_token'));
    }

    public function tearDown(): void
    {
        self::$users = [];

        $this->webAuth->signout();
    }

    public function testWebAdapterConstructor()
    {
        $this->assertInstanceOf(SessionAuthAdapter::class, $this->webAuth);
    }

    public function testWebSigninIncorrectCredentials()
    {
        $this->expectException(AuthException::class);

        $this->expectExceptionMessage('incorrect_auth_credentials');

        $this->webAuth->signin('admin@qt.com', '111111');
    }

    public function testWebSigninCorrectCredentials()
    {
        $this->assertTrue($this->webAuth->signin('admin@qt.com', 'qwerty'));

        $this->assertTrue($this->webAuth->signin('admin@qt.com', 'qwerty', true));
    }

    public function testWebSigninWithRemember()
    {
        $this->webAuth->signin('admin@qt.com', 'qwerty', true);

        session()->delete('auth_user');

        $this->assertTrue($this->webAuth->check());
    }

    public function testWebSignout()
    {
        $this->assertFalse($this->webAuth->check());

        $this->webAuth->signin('admin@qt.com', 'qwerty');

        $this->assertTrue($this->webAuth->check());

        $this->webAuth->signout();

        $this->assertFalse($this->webAuth->check());
    }

    public function testWebUser()
    {
        $this->webAuth->signin('admin@qt.com', 'qwerty');

        $this->assertInstanceOf(User::class, $this->webAuth->user());

        $this->assertEquals('admin@qt.com', $this->webAuth->user()->getFieldValue('email'));

        $this->assertEquals('admin', $this->webAuth->user()->getFieldValue('role'));
    }

    public function testWebCheck()
    {
        $this->assertFalse($this->webAuth->check());

        $this->webAuth->signin('admin@qt.com', 'qwerty');

        $this->assertTrue($this->webAuth->check());
    }

    public function testWebSignupAndSigninWithoutActivation()
    {

        $this->expectException(AuthException::class);

        $this->expectExceptionMessage('inactive_account');

        $this->webAuth->signup($this->guestUser);

        $this->assertTrue($this->webAuth->signin('guest@qt.com', '123456'));
    }

    public function testWebSignupAndActivateAccount()
    {
        $user = $this->webAuth->signup($this->guestUser);

        $this->webAuth->activate($user->getFieldValue('activation_token'));

        $this->assertTrue($this->webAuth->signin('guest@qt.com', '123456'));
    }

    public function testWebForgetReset()
    {
        $resetToken = $this->webAuth->forget('admin@qt.com', 'tpl');

        $this->webAuth->reset($resetToken, '123456789');

        $this->assertTrue($this->webAuth->signin('admin@qt.com', '123456789'));
    }

    public function testWebWithoutVerification()
    {
        config()->set('2FA', false);

        config()->set('otp_expiry_time', 2);

        $this->assertTrue($this->webAuth->signin('admin@qt.com', 'qwerty'));
    }

    public function testWebWithVerification()
    {
        config()->set('2FA', true);

        config()->set('otp_expires', 2);

        $this->assertIsString($this->webAuth->signin('admin@qt.com', 'qwerty'));
    }

    public function testWebVerify()
    {
        config()->set('2FA', true);

        config()->set('otp_expires', 2);

        $otp_token = $this->webAuth->signin('admin@qt.com', 'qwerty');

        $this->assertTrue($this->webAuth->verifyOtp(123456789, $otp_token));
    }

    public function testWebResendOtp()
    {
        config()->set('2FA', true);

        config()->set('otp_expires', 2);

        $otp_token = $this->webAuth->signin('admin@qt.com', 'qwerty');

        $this->assertIsString($this->webAuth->resendOtp($otp_token));
    }
}