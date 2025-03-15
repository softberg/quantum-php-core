<?php

namespace Quantum\Tests\Unit\Libraries\Auth\Adapters;

use Quantum\Libraries\Auth\Adapters\JwtAuthAdapter;
use Quantum\Libraries\Auth\Exceptions\AuthException;
use Quantum\Tests\Unit\Libraries\Auth\AuthTestCase;
use Quantum\Libraries\Hasher\Hasher;
use Quantum\Libraries\Jwt\JwtToken;
use Quantum\Libraries\Auth\User;

class SessionAuthAdapterTest extends AuthTestCase
{
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
                'exp' => time() + 60
            ]);

        $this->apiAuth = new JwtAuthAdapter($this->authService, $this->mailer, new Hasher, $jwt);


        $admin = $this->apiAuth->signup($this->adminUser);

        $this->apiAuth->activate($admin->getFieldValue('activation_token'));
    }

    public function tearDown(): void
    {
        self::$users = [];

        $this->apiAuth->signout();
    }

    public function testApiAdapterConstructor()
    {
        $this->assertInstanceOf(JwtAuthAdapter::class, $this->apiAuth);
    }

    public function testApiSigninIncorrectCredentials()
    {
        $this->expectException(AuthException::class);

        $this->expectExceptionMessage('incorrect_auth_credentials');

        $this->apiAuth->signin('admin@qt.com', '111111');
    }

    public function testApiSigninCorrectCredentials()
    {
        config()->set('2FA', false);

        $this->assertIsArray($this->apiAuth->signin('admin@qt.com', 'qwerty'));

        $this->assertArrayHasKey('access_token', $this->apiAuth->signin('admin@qt.com', 'qwerty'));

        $this->assertArrayHasKey('refresh_token', $this->apiAuth->signin('admin@qt.com', 'qwerty'));
    }

    public function testApiSignOut()
    {
        $this->apiAuth->signin('admin@qt.com', 'qwerty');

        $this->assertTrue($this->apiAuth->check());

        $this->apiAuth->signout();

        $this->assertFalse($this->apiAuth->check());
    }

    public function testApiUser()
    {
        $this->apiAuth->signin('admin@qt.com', 'qwerty');

        $this->assertInstanceOf(User::class, $this->apiAuth->user());

        $this->assertEquals('admin@qt.com', $this->apiAuth->user()->getFieldValue('email'));

        $this->assertEquals('admin', $this->apiAuth->user()->getFieldValue('role'));

        $this->apiAuth->signout();

        $this->assertNull($this->apiAuth->user());
    }

    public function testApiCheck()
    {
        $this->assertFalse($this->apiAuth->check());

        $this->apiAuth->signin('admin@qt.com', 'qwerty');

        $this->assertTrue($this->apiAuth->check());

        $this->apiAuth->signout();

        $this->assertFalse($this->apiAuth->check());
    }

    public function testApiSignupAndSigninWithoutActivation()
    {
        $this->expectException(AuthException::class);

        $this->expectExceptionMessage('inactive_account');

        $this->apiAuth->signup($this->guestUser);

        $this->assertTrue($this->apiAuth->signin('guest@qt.com', '123456'));
    }

    public function testApiSignupAndActivateAccount()
    {
        $user = $this->apiAuth->signup($this->guestUser);

        $this->apiAuth->activate($user->getFieldValue('activation_token'));

        $this->assertIsArray($this->apiAuth->signin('guest@qt.com', '123456'));

        $this->assertArrayHasKey('access_token', $this->apiAuth->signin('guest@qt.com', '123456'));

        $this->assertArrayHasKey('refresh_token', $this->apiAuth->signin('guest@qt.com', '123456'));
    }

    public function testApiForgetReset()
    {
        $resetToken = $this->apiAuth->forget('admin@qt.com', 'tpl');

        $this->apiAuth->reset($resetToken, '123456789');

        $this->assertIsArray($this->apiAuth->signin('admin@qt.com', '123456789'));

        $this->assertArrayHasKey('access_token', $this->apiAuth->signin('admin@qt.com', '123456789'));

        $this->assertArrayHasKey('refresh_token', $this->apiAuth->signin('admin@qt.com', '123456789'));
    }

    public function testApiVerify()
    {
        config()->set('2FA', true);

        config()->set('otp_expires', 2);

        $otp_token = $this->apiAuth->signin('admin@qt.com', 'qwerty');

        $tokens = $this->apiAuth->verifyOtp(123456789, $otp_token);

        $this->assertArrayHasKey('access_token', $tokens);

        $this->assertArrayHasKey('refresh_token', $tokens);
    }

    public function testApiSigninWithoutVerification()
    {
        config()->set('2FA', false);

        config()->set('otp_expires', 2);

        $this->assertArrayHasKey('access_token', $this->apiAuth->signin('admin@qt.com', 'qwerty'));

        $this->assertArrayHasKey('refresh_token', $this->apiAuth->signin('admin@qt.com', 'qwerty'));
    }

    public function testApiSigninWithVerification()
    {
        config()->set('2FA', true);

        config()->set('otp_expires', 2);

        $this->assertIsString($this->apiAuth->signin('admin@qt.com', 'qwerty'));
    }

    public function testApiResendOtp()
    {
        config()->set('2FA', true);

        config()->set('otp_expires', 2);

        $otp_token = $this->apiAuth->signin('admin@qt.com', 'qwerty');

        $this->assertIsString($this->apiAuth->resendOtp($otp_token));
    }

    public function testApiRefreshUser()
    {
        $this->apiAuth->signin('admin@qt.com', 'qwerty');

        $this->assertEquals('Admin', $this->apiAuth->user()->firstname);

        $this->assertEquals('User', $this->apiAuth->user()->lastname);

        $newUserData = [
            'firstname' => 'Super',
            'lastname' => 'Human',
        ];

        $this->authService->update('email', $this->apiAuth->user()->email, $newUserData);

        $this->apiAuth->refreshUser();

        $this->assertEquals('Super', $this->apiAuth->user()->firstname);

        $this->assertEquals('Human', $this->apiAuth->user()->lastname);
    }
}