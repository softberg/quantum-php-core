<?php

namespace Quantum\Tests\Unit\Libraries\Auth\Factories;

use Quantum\Tests\_root\modules\Test\Services\AuthService;
use Quantum\Libraries\Auth\Exceptions\AuthException;
use Quantum\Libraries\Auth\Factories\AuthFactory;
use Quantum\Libraries\Auth\Adapters\ApiAdapter;
use Quantum\Libraries\Auth\Adapters\WebAdapter;
use Quantum\Tests\Unit\AppTestCase;
use Quantum\Libraries\Auth\Auth;

class AuthFactoryTest extends AppTestCase
{

    public function setUp(): void
    {
        parent::setUp();

        $this->setPrivateProperty(AuthFactory::class, 'instance', null);
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
        config()->set('auth.service', AuthService::class);

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