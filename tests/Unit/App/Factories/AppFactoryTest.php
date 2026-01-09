<?php

namespace Quantum\Tests\Unit\App\Factories;

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
        $app = AppFactory::create(App::WEB, PROJECT_ROOT);

        $this->assertInstanceOf(App::class, $app);
    }

    public function tearDown(): void
    {
        config()->flush();
    }

    public function testAppFactoryConsoleAdapter()
    {
        $app = AppFactory::create(App::CONSOLE, PROJECT_ROOT);

        $this->assertInstanceOf(ConsoleAppAdapter::class, $app->getAdapter());

        $this->assertInstanceOf(AppInterface::class, $app->getAdapter());
    }

    public function testAppFactoryWebAdapter()
    {
        $app = AppFactory::create(App::WEB, PROJECT_ROOT);

        $this->assertInstanceOf(WebAppAdapter::class, $app->getAdapter());

        $this->assertInstanceOf(AppInterface::class, $app->getAdapter());
    }

    public function testAppFactoryInvalidAdapter()
    {
        $this->expectException(AppException::class);

        $this->expectExceptionMessage('The adapter `invalid_type` is not supported');

        AppFactory::create('invalid_type', PROJECT_ROOT);
    }

    public function testAppFactoryReturnsSameInstance()
    {
        $app1 = AppFactory::create(App::WEB, PROJECT_ROOT);
        $app2 = AppFactory::create(App::WEB, PROJECT_ROOT);

        $this->assertSame($app1, $app2);
    }

    public function testAppFactoryDestroy()
    {
        $app1 = AppFactory::create(App::WEB, PROJECT_ROOT);

        AppFactory::destroy(App::WEB);

        $app2 = AppFactory::create(App::WEB, PROJECT_ROOT);

        $this->assertInstanceOf(App::class, $app2);

        $this->assertNotSame($app1, $app2);
    }
}
