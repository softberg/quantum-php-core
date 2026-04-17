<?php

namespace Quantum\Tests\Unit\App;

use Quantum\Router\RouteCollection;
use Quantum\Environment\Environment;
use PHPUnit\Framework\TestCase;
use Quantum\App\Enums\AppType;
use InvalidArgumentException;
use Quantum\Di\DiContainer;
use Quantum\App\AppContext;
use Quantum\Config\Config;
use Quantum\Http\Response;
use Quantum\Http\Request;
use Mockery;

class AppContextTest extends TestCase
{
    public function testAppContextWebMode(): void
    {
        $context = new AppContext(AppType::WEB, '', new DiContainer());

        $this->assertSame(AppType::WEB, $context->getMode());
        $this->assertTrue($context->isWebMode());
        $this->assertFalse($context->isConsoleMode());
    }

    public function testAppContextConsoleMode(): void
    {
        $context = new AppContext(AppType::CONSOLE, '', new DiContainer());

        $this->assertSame(AppType::CONSOLE, $context->getMode());
        $this->assertFalse($context->isWebMode());
        $this->assertTrue($context->isConsoleMode());
    }

    public function testAppContextRejectsInvalidMode(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid app mode: invalid');

        new AppContext('invalid', '', new DiContainer());
    }

    public function testAppContextBaseDir(): void
    {
        $context = new AppContext(AppType::WEB, '/my/base/dir', new DiContainer());

        $this->assertSame('/my/base/dir', $context->getBaseDir());
    }

    public function testAppContextContainer(): void
    {
        $container = new DiContainer();
        $context = new AppContext(AppType::WEB, '/tmp', $container);

        $this->assertSame($container, $context->getContainer());
    }

    public function testAppContextGetEnvironment(): void
    {
        $container = new DiContainer();
        $environment = Mockery::mock(Environment::class);
        $container->set(Environment::class, $environment);

        $context = new AppContext(AppType::WEB, '/tmp', $container);

        $this->assertSame($environment, $context->getEnvironment());
    }

    public function testAppContextGetConfig(): void
    {
        $container = new DiContainer();
        $config = Mockery::mock(Config::class);
        $container->set(Config::class, $config);

        $context = new AppContext(AppType::WEB, '/tmp', $container);

        $this->assertSame($config, $context->getConfig());
    }

    public function testAppContextGetRequest(): void
    {
        $container = new DiContainer();
        $request = Mockery::mock(Request::class);
        $container->set(Request::class, $request);

        $context = new AppContext(AppType::WEB, '/tmp', $container);

        $this->assertSame($request, $context->getRequest());
    }

    public function testAppContextGetResponse(): void
    {
        $container = new DiContainer();
        $response = Mockery::mock(Response::class);
        $container->set(Response::class, $response);

        $context = new AppContext(AppType::WEB, '/tmp', $container);

        $this->assertSame($response, $context->getResponse());
    }

    public function testAppContextGetRoutes(): void
    {
        $container = new DiContainer();
        $routes = new RouteCollection();
        $container->set(RouteCollection::class, $routes);

        $context = new AppContext(AppType::WEB, '/tmp', $container);

        $this->assertSame($routes, $context->getRoutes());
    }
}
