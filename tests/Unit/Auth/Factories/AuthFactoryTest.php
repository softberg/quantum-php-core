<?php

namespace Quantum\Tests\Unit\Auth\Factories;

use Quantum\Auth\Adapters\SessionAuthAdapter;
use Quantum\Auth\Exceptions\AuthException;
use Quantum\Auth\Adapters\JwtAuthAdapter;
use Quantum\Auth\Factories\AuthFactory;
use Quantum\Tests\Unit\AppTestCase;
use Quantum\Auth\Enums\AuthType;
use Quantum\Auth\Auth;
use Quantum\Di\Di;

class AuthFactoryTest extends AppTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->resetAuthFactory();
    }

    public function testAuthFactoryInstance(): void
    {
        $auth = AuthFactory::get();

        $this->assertInstanceOf(Auth::class, $auth);
    }

    public function testAuthFactoryDefaultAuthAdapter(): void
    {
        $auth = AuthFactory::get();

        $this->assertInstanceOf(SessionAuthAdapter::class, $auth->getAdapter());
    }

    public function testAuthFactorySessionAuthAdapter(): void
    {
        $auth = AuthFactory::get(AuthType::SESSION);

        $this->assertInstanceOf(SessionAuthAdapter::class, $auth->getAdapter());
    }

    public function testAuthFactoryJwtAuthAdapter(): void
    {
        $auth = AuthFactory::get(AuthType::JWT);

        $this->assertInstanceOf(JwtAuthAdapter::class, $auth->getAdapter());
    }

    public function testAuthFactoryInvalidTypeAdapter(): void
    {
        config()->set('auth.default', 'invalid');

        $this->expectException(AuthException::class);

        $this->expectExceptionMessage('The adapter `invalid` is not supported');

        AuthFactory::get();
    }

    public function testAuthFactoryReturnsSameInstance(): void
    {
        $auth1 = AuthFactory::get(AuthType::SESSION);
        $auth2 = AuthFactory::get(AuthType::SESSION);

        $this->assertSame($auth1, $auth2);
    }

    private function resetAuthFactory(): void
    {
        if (!Di::isRegistered(AuthFactory::class)) {
            Di::register(AuthFactory::class);
        }

        $factory = Di::get(AuthFactory::class);
        $this->setPrivateProperty($factory, 'instances', []);
    }
}
