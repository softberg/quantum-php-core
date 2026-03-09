<?php

namespace Quantum\Tests\Unit\Auth;

use Quantum\Auth\Contracts\AuthenticatableInterface;
use Quantum\Auth\Adapters\SessionAuthAdapter;
use Quantum\Auth\Exceptions\AuthException;
use Quantum\Auth\Adapters\JwtAuthAdapter;
use Quantum\Jwt\JwtToken;
use Quantum\Hasher\Hasher;
use Quantum\Auth\Auth;

class AuthTest extends AuthTestCase
{
    private JwtToken $jwt;

    public function setUp(): void
    {
        parent::setUp();

        $this->jwt = (new JwtToken())
            ->setLeeway(1)
            ->setClaims([
                'jti' => uniqid(),
                'iss' => 'issuer',
                'aud' => 'audience',
                'iat' => time(),
                'nbf' => time() + 1,
                'exp' => time() + 60,
            ]);
    }

    public function testAuthGetAdapter(): void
    {
        $auth = new Auth(new SessionAuthAdapter($this->authService, $this->mailer, new Hasher()));

        $this->assertInstanceOf(AuthenticatableInterface::class, $auth->getAdapter());

        $auth = new Auth(new JwtAuthAdapter($this->authService, $this->mailer, new Hasher(), $this->jwt));

        $this->assertInstanceOf(AuthenticatableInterface::class, $auth->getAdapter());
    }

    public function testAuthCallingValidMethod(): void
    {
        $auth = new Auth(new JwtAuthAdapter($this->authService, $this->mailer, new Hasher(), $this->jwt));

        $user = $auth->getAdapter()->signup($this->adminUser);

        $auth->getAdapter()->activate($user->getFieldValue('activation_token'));

        $this->assertIsArray($auth->signin('admin@qt.com', 'qwerty'));

        $this->assertArrayHasKey('access_token', $auth->signin('admin@qt.com', 'qwerty'));

        $this->assertArrayHasKey('refresh_token', $auth->signin('admin@qt.com', 'qwerty'));
    }

    public function testAuthCallingInvalidMethod(): void
    {
        $auth = new Auth(new JwtAuthAdapter($this->authService, $this->mailer, new Hasher(), $this->jwt));

        $this->expectException(AuthException::class);

        $this->expectExceptionMessage('The method `callingInvalidMethod` is not supported for `' . JwtAuthAdapter::class . '`');

        $auth->callingInvalidMethod();
    }
}
