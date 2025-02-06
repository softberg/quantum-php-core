<?php

namespace Quantum\Tests\Unit\Libraries\Auth;

use Quantum\Libraries\Auth\Contracts\AuthenticatableInterface;
use Quantum\Libraries\Auth\Exceptions\AuthException;
use Quantum\Libraries\Auth\Adapters\ApiAdapter;
use Quantum\Libraries\Auth\Adapters\WebAdapter;
use Quantum\Libraries\Jwt\JwtToken;
use Quantum\Libraries\Hasher\Hasher;
use Quantum\Libraries\Auth\Auth;

class AuthTest extends AuthTestCase
{

    private $jwt;

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
                'exp' => time() + 60
            ]);
    }

    public function testAuthGetAdapter()
    {
        $auth = new Auth(new WebAdapter($this->authService, $this->mailer, new Hasher));

        $this->assertInstanceOf(AuthenticatableInterface::class, $auth->getAdapter());

        $auth = new Auth(new ApiAdapter($this->authService, $this->mailer, new Hasher, $this->jwt));

        $this->assertInstanceOf(AuthenticatableInterface::class, $auth->getAdapter());

        $this->assertInstanceOf(AuthenticatableInterface::class, $auth->getAdapter());
    }

    public function testAuthCallingValidMethod()
    {
        $auth = new Auth(new ApiAdapter($this->authService, $this->mailer, new Hasher, $this->jwt));

        $user = $auth->getAdapter()->signup($this->adminUser);

        $auth->getAdapter()->activate($user->getFieldValue('activation_token'));

        $this->assertIsArray($auth->signin('admin@qt.com', 'qwerty'));

        $this->assertArrayHasKey('access_token', $auth->signin('admin@qt.com', 'qwerty'));

        $this->assertArrayHasKey('refresh_token', $auth->signin('admin@qt.com', 'qwerty'));
    }

    public function testAuthCallingInvalidMethod()
    {
        $auth = new Auth(new ApiAdapter($this->authService, $this->mailer, new Hasher, $this->jwt));

        $this->expectException(AuthException::class);

        $this->expectExceptionMessage('The method `callingInvalidMethod` is not supported for `' . ApiAdapter::class . '`');

        $auth->callingInvalidMethod();
    }
}