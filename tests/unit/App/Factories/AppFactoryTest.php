<?php

namespace Quantum\Tests\App\Factories;

use Quantum\App\Adapters\ConsoleAppAdapter;
use Quantum\App\Exceptions\AppException;
use Quantum\App\Contracts\AppInterface;
use Quantum\App\Adapters\WebAppAdapter;
use Quantum\App\Factories\AppFactory;
use PHPUnit\Framework\TestCase;
use Quantum\App\App;


class AppFactoryTest extends TestCase
{

    public function setUp(): void
    {
        parent::setUp();
    }

    public function testAppFactoryInstance()
    {
        $app = AppFactory::create(App::WEB, dirname(__DIR__, 2) . DS . '_root');

        $this->assertInstanceOf(App::class, $app);
    }

    public function testAppFactoryPharAdapter()
    {
        $app = AppFactory::create(App::CONSOLE, dirname(__DIR__, 2) . DS . '_root');

        $this->assertInstanceOf(ConsoleAppAdapter::class, $app->getAdapter());

        $this->assertInstanceOf(AppInterface::class, $app->getAdapter());
    }

    public function testAppFactoryWebAdapter()
    {
        $app = AppFactory::create(App::WEB, dirname(__DIR__, 2) . DS . '_root');

        $this->assertInstanceOf(WebAppAdapter::class, $app->getAdapter());

        $this->assertInstanceOf(AppInterface::class, $app->getAdapter());
    }

    public function testAppFactoryInvalidAdapter()
    {
        $this->expectException(AppException::class);

        $this->expectExceptionMessage('The adapter `invalid_type` is not supported`');

        AppFactory::create('invalid_type', __DIR__);
    }

    public function testAppFactoryReturnsSameInstance()
    {
        $app1 = AppFactory::create(App::WEB, dirname(__DIR__, 2) . DS . '_root');
        $app2 = AppFactory::create(App::WEB, dirname(__DIR__, 2) . DS . '_root');

        $this->assertSame($app1, $app2);
    }
}