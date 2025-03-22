<?php

namespace Quantum\Tests\Unit\Libraries\Auth\Factories;

use Quantum\Libraries\Auth\Adapters\SessionAuthAdapter;
use Quantum\Libraries\Auth\Adapters\JwtAuthAdapter;
use Quantum\Libraries\Auth\Exceptions\AuthException;
use Quantum\Libraries\Auth\Factories\AuthFactory;
use Quantum\Tests\Unit\AppTestCase;
use Quantum\Libraries\Auth\Auth;

class AuthFactoryTest extends AppTestCase
{

    public function setUp(): void
    {
        parent::setUp();

        $this->setPrivateProperty(AuthFactory::class, 'instances', []);
    }

    public function testAuthFactoryInstance()
    {
        $auth = AuthFactory::get();

        $this->assertInstanceOf(Auth::class, $auth);
    }

    public function testAuthFactoryDefaultAuthAdapter()
    {
        $auth = AuthFactory::get();

        $this->assertInstanceOf(SessionAuthAdapter::class, $auth->getAdapter());
    }

    public function testAuthFactorySessionAuthAdapter()
    {
        $auth = AuthFactory::get(Auth::SESSION);

        $this->assertInstanceOf(SessionAuthAdapter::class, $auth->getAdapter());
    }

    public function testAuthFactoryJwtAuthAdapter()
    {
        $auth = AuthFactory::get(Auth::JWT);

        $this->assertInstanceOf(JwtAuthAdapter::class, $auth->getAdapter());
    }

    public function testAuthFactoryInvalidTypeAdapter()
    {
        config()->set('auth.default', 'invalid');

        $this->expectException(AuthException::class);

        $this->expectExceptionMessage('The adapter `invalid` is not supported`');

        AuthFactory::get();
    }

    public function testAuthFactoryReturnsSameInstance()
    {
        $auth1 = AuthFactory::get(Auth::SESSION);
        $auth2 = AuthFactory::get(Auth::SESSION);

        $this->assertSame($auth1, $auth2);
    }
}