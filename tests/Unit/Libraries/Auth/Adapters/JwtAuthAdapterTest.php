<?php

namespace Quantum\Tests\Unit\Libraries\Auth\Adapters;

use Quantum\Libraries\Auth\Exceptions\AuthException;
use Quantum\Libraries\Auth\Adapters\JwtAuthAdapter;
use Quantum\Libraries\Auth\Enums\ExceptionMessages;
use Quantum\Tests\Unit\Libraries\Auth\AuthTestCase;
use Quantum\Libraries\Hasher\Hasher;
use Quantum\Libraries\Jwt\JwtToken;
use Quantum\Libraries\Auth\User;

class JwtAuthAdapterTest extends AuthTestCase
{
    private $jwtAuth;

    public function setUp(): void
    {

        parent::setUp();

        $jwt = (new JwtToken())
            ->setLeeway(1)
            ->setClaims([
                'jti' => uniqid(),
                'iss' => 'issuer',
                'aud' => 'audience',
                'iat' => time(),
                'nbf' => time() + 1,
                'exp' => time() + 60,
            ]);

        $this->jwtAuth = new JwtAuthAdapter($this->authService, $this->mailer, new Hasher(), $jwt);

        $admin = $this->jwtAuth->signup($this->adminUser);

        $this->jwtAuth->activate($admin->getFieldValue('activation_token'));
    }

    public function tearDown(): void
    {
        self::$users = [];

        $this->jwtAuth->signout();
    }

    public function testApiAdapterConstructor()
    {
        $this->assertInstanceOf(JwtAuthAdapter::class, $this->jwtAuth);
    }

    public function testApiSigninIncorrectCredentials()
    {
        $this->expectException(AuthException::class);

        $this->expectExceptionMessage(ExceptionMessages::INCORRECT_CREDENTIALS);

        $this->jwtAuth->signin('admin@qt.com', '111111');
    }

    public function testApiSigninCorrectCredentials()
    {
        config()->set('TWO_FA', false);

        $this->assertIsArray($this->jwtAuth->signin('admin@qt.com', 'qwerty'));

        $this->assertArrayHasKey('access_token', $this->jwtAuth->signin('admin@qt.com', 'qwerty'));

        $this->assertArrayHasKey('refresh_token', $this->jwtAuth->signin('admin@qt.com', 'qwerty'));
    }

    public function testApiSignOut()
    {
        $this->jwtAuth->signin('admin@qt.com', 'qwerty');

        $this->assertTrue($this->jwtAuth->check());

        $this->jwtAuth->signout();

        $this->assertFalse($this->jwtAuth->check());
    }

    public function testApiUser()
    {
        $this->jwtAuth->signin('admin@qt.com', 'qwerty');

        $this->assertInstanceOf(User::class, $this->jwtAuth->user());

        $this->assertEquals('admin@qt.com', $this->jwtAuth->user()->getFieldValue('email'));

        $this->assertEquals('admin', $this->jwtAuth->user()->getFieldValue('role'));

        $this->jwtAuth->signout();

        $this->assertNull($this->jwtAuth->user());
    }

    public function testApiCheck()
    {
        $this->assertFalse($this->jwtAuth->check());

        $this->jwtAuth->signin('admin@qt.com', 'qwerty');

        $this->assertTrue($this->jwtAuth->check());

        $this->jwtAuth->signout();

        $this->assertFalse($this->jwtAuth->check());
    }

    public function testApiSignupAndSigninWithoutActivation()
    {
        $this->expectException(AuthException::class);

        $this->expectExceptionMessage(ExceptionMessages::INACTIVE_ACCOUNT);

        $this->jwtAuth->signup($this->guestUser);

        $this->assertTrue($this->jwtAuth->signin('guest@qt.com', '123456'));
    }

    public function testApiSignupAndActivateAccount()
    {
        $user = $this->jwtAuth->signup($this->guestUser);

        $this->jwtAuth->activate($user->getFieldValue('activation_token'));

        $this->assertIsArray($this->jwtAuth->signin('guest@qt.com', '123456'));

        $this->assertArrayHasKey('access_token', $this->jwtAuth->signin('guest@qt.com', '123456'));

        $this->assertArrayHasKey('refresh_token', $this->jwtAuth->signin('guest@qt.com', '123456'));
    }

    public function testApiForgetReset()
    {
        $resetToken = $this->jwtAuth->forget('admin@qt.com', 'tpl');

        $this->jwtAuth->reset($resetToken, '123456789');

        $this->assertIsArray($this->jwtAuth->signin('admin@qt.com', '123456789'));

        $this->assertArrayHasKey('access_token', $this->jwtAuth->signin('admin@qt.com', '123456789'));

        $this->assertArrayHasKey('refresh_token', $this->jwtAuth->signin('admin@qt.com', '123456789'));
    }

    public function testApiVerifyOtp()
    {
        config()->set('auth.two_fa', true);

        config()->set('auth.otp_expires', 2);

        $otp_token = $this->jwtAuth->signin('admin@qt.com', 'qwerty');

        $tokens = $this->jwtAuth->verifyOtp(123456789, $otp_token);

        $this->assertArrayHasKey('access_token', $tokens);

        $this->assertArrayHasKey('refresh_token', $tokens);
    }

    public function testApiSigninWithoutVerification()
    {
        config()->set('auth.two_fa', false);

        config()->set('auth.otp_expires', 2);

        $this->assertArrayHasKey('access_token', $this->jwtAuth->signin('admin@qt.com', 'qwerty'));

        $this->assertArrayHasKey('refresh_token', $this->jwtAuth->signin('admin@qt.com', 'qwerty'));
    }

    public function testApiSigninWithVerification()
    {
        config()->set('auth.two_fa', true);

        config()->set('auth.otp_expires', 2);

        $this->assertIsString($this->jwtAuth->signin('admin@qt.com', 'qwerty'));
    }

    public function testApiResendOtp()
    {
        config()->set('auth.two_fa', true);

        config()->set('auth.otp_expires', 2);

        $otp_token = $this->jwtAuth->signin('admin@qt.com', 'qwerty');

        $this->assertIsString($this->jwtAuth->resendOtp($otp_token));
    }

    public function testApiRefreshUser()
    {
        $this->jwtAuth->signin('admin@qt.com', 'qwerty');

        $user = $this->jwtAuth->user();

        $this->assertEquals('Admin', $user->firstname);

        $this->assertEquals('User', $user->lastname);

        $newUserData = [
            'firstname' => 'Super',
            'lastname' => 'Human',
        ];

        $this->authService->update('email', $this->jwtAuth->user()->email, $newUserData);

        $this->jwtAuth->refreshUser($this->jwtAuth->user()->uuid);

        $refreshedUser = $this->jwtAuth->user();

        $this->assertEquals('Super', $refreshedUser->firstname);

        $this->assertEquals('Human', $refreshedUser->lastname);
    }
}
