<?php

namespace Quantum\Tests\Unit\Auth\Adapters;

use Quantum\Auth\Adapters\SessionAuthAdapter;
use Quantum\Auth\Exceptions\AuthException;
use Quantum\Auth\Enums\ExceptionMessages;
use Quantum\Tests\Unit\Auth\AuthTestCase;
use Quantum\Hasher\Hasher;
use Quantum\Auth\User;

class SessionAuthAdapterTest extends AuthTestCase
{
    private SessionAuthAdapter $sessionAuth;

    public function setUp(): void
    {
        parent::setUp();

        config()->set('auth.two_fa', false);

        $this->sessionAuth = new SessionAuthAdapter($this->authService, $this->mailer, new Hasher());

        $admin = $this->sessionAuth->signup($this->adminUser);

        $this->sessionAuth->activate($admin->getFieldValue('activation_token'));
    }

    public function tearDown(): void
    {
        self::$users = [];

        $this->sessionAuth->signout();
    }

    public function testWebAdapterConstructor(): void
    {
        $this->assertInstanceOf(SessionAuthAdapter::class, $this->sessionAuth);
    }

    public function testWebSigninIncorrectCredentials(): void
    {
        $this->expectException(AuthException::class);

        $this->expectExceptionMessage(ExceptionMessages::INCORRECT_CREDENTIALS);

        $this->sessionAuth->signin('admin@qt.com', '111111');
    }

    public function testWebSigninCorrectCredentials(): void
    {
        $this->assertTrue($this->sessionAuth->signin('admin@qt.com', 'qwerty'));

        $this->assertTrue($this->sessionAuth->signin('admin@qt.com', 'qwerty', true));
    }

    public function testWebSigninWithRemember(): void
    {
        $this->sessionAuth->signin('admin@qt.com', 'qwerty', true);

        session()->delete('auth_user');

        $this->assertTrue($this->sessionAuth->check());
    }

    public function testWebSignout(): void
    {
        $this->assertFalse($this->sessionAuth->check());

        $this->sessionAuth->signin('admin@qt.com', 'qwerty');

        $this->assertTrue($this->sessionAuth->check());

        $this->sessionAuth->signout();

        $this->assertFalse($this->sessionAuth->check());
    }

    public function testWebUser(): void
    {
        $this->sessionAuth->signin('admin@qt.com', 'qwerty');

        $this->assertInstanceOf(User::class, $this->sessionAuth->user());

        $this->assertEquals('admin@qt.com', $this->sessionAuth->user()->getFieldValue('email'));

        $this->assertEquals('admin', $this->sessionAuth->user()->getFieldValue('role'));
    }

    public function testWebCheck(): void
    {
        $this->assertFalse($this->sessionAuth->check());

        $this->sessionAuth->signin('admin@qt.com', 'qwerty');

        $this->assertTrue($this->sessionAuth->check());
    }

    public function testWebSignupAndSigninWithoutActivation(): void
    {

        $this->expectException(AuthException::class);

        $this->expectExceptionMessage(ExceptionMessages::INACTIVE_ACCOUNT);

        $this->sessionAuth->signup($this->guestUser);

        $this->assertTrue($this->sessionAuth->signin('guest@qt.com', '123456'));
    }

    public function testWebSignupAndActivateAccount(): void
    {
        $user = $this->sessionAuth->signup($this->guestUser);

        $this->sessionAuth->activate($user->getFieldValue('activation_token'));

        $this->assertTrue($this->sessionAuth->signin('guest@qt.com', '123456'));
    }

    public function testWebForgetReset(): void
    {
        $resetToken = $this->sessionAuth->forget('admin@qt.com');

        $this->sessionAuth->reset($resetToken, '123456789');

        $this->assertTrue($this->sessionAuth->signin('admin@qt.com', '123456789'));
    }

    public function testWebVerifyOtp(): void
    {
        config()->set('auth.two_fa', true);

        config()->set('auth.otp_expires', 2);

        $otp_token = $this->sessionAuth->signin('admin@qt.com', 'qwerty');

        $this->assertTrue($this->sessionAuth->verifyOtp(123456789, $otp_token));
    }

    public function testWebSigninWithoutVerification(): void
    {
        config()->set('auth.two_fa', false);

        config()->set('auth.otp_expires', 2);

        $this->assertTrue($this->sessionAuth->signin('admin@qt.com', 'qwerty'));
    }

    public function testWebSigninWithVerification(): void
    {
        config()->set('auth.two_fa', true);

        config()->set('auth.otp_expires', 2);

        $this->assertIsString($this->sessionAuth->signin('admin@qt.com', 'qwerty'));
    }

    public function testWebResendOtp(): void
    {
        config()->set('auth.two_fa', true);

        config()->set('auth.otp_expires', 2);

        $otp_token = $this->sessionAuth->signin('admin@qt.com', 'qwerty');

        $this->assertIsString($this->sessionAuth->resendOtp($otp_token));
    }

    public function testWebRefreshUser(): void
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
