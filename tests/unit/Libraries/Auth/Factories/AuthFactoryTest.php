<?php

namespace Quantum\Tests\Libraries\Auth\Factories;

use Quantum\Libraries\Auth\Exceptions\AuthException;
use Quantum\Libraries\Auth\Factories\AuthFactory;
use Quantum\Libraries\Auth\Adapters\ApiAdapter;
use Quantum\Libraries\Auth\Adapters\WebAdapter;
use Quantum\Libraries\Auth\Auth;
use Quantum\Tests\AppTestCase;
use ReflectionClass;

class AuthFactoryTest extends AppTestCase
{

    public function setUp(): void
    {
        parent::setUp();

        $reflection = new ReflectionClass(AuthFactory::class);
        $property = $reflection->getProperty('instance');
        $property->setAccessible(true);
        $property->setValue(null);
    }

    public function testAuthFactoryInstance()
    {
        $auth = AuthFactory::get();

        $this->assertInstanceOf(Auth::class, $auth);
    }

    public function testAuthFactoryWebAdapter()
    {
        $auth = AuthFactory::get();

        $this->assertInstanceOf(WebAdapter::class, $auth->getAdapter());
    }

    public function testAuthFactoryApiAdapter()
    {
        config()->set('auth.type', 'api');
        config()->set('auth.service', \Quantum\Tests\_root\modules\Test\Services\AuthService::class);

        $auth = AuthFactory::get();

        $this->assertInstanceOf(ApiAdapter::class, $auth->getAdapter());
    }

    public function testAuthFactoryInvalidTypeAdapter()
    {
        config()->set('auth.type', 'invalid');

        $this->expectException(AuthException::class);

        $this->expectExceptionMessage('The adapter `invalid` is not supported`');

        AuthFactory::get();
    }

    public function testAuthFactoryReturnsSameInstance()
    {
        $auth1 = AuthFactory::get();
        $auth2 = AuthFactory::get();

        $this->assertSame($auth1, $auth2);
    }
}