<?php

namespace Quantum\Tests\Unit;

use Quantum\Storage\Factories\FileSystemFactory;
use Quantum\App\Factories\AppFactory;
use Quantum\Router\MatchedRoute;
use Quantum\Storage\FileSystem;
use PHPUnit\Framework\TestCase;
use Quantum\Debugger\Debugger;
use Quantum\App\Enums\AppType;
use Quantum\Di\DiContainer;
use Quantum\App\AppContext;
use Quantum\Config\Config;
use Quantum\Router\Route;
use ReflectionClass;
use Quantum\App\App;
use Quantum\Di\Di;
use ReflectionProperty;

abstract class AppTestCase extends TestCase
{
    protected AppContext $context;

    /** @var FileSystem */
    protected $fs;

    public function setUp(): void
    {
        AppFactory::create(AppType::WEB, PROJECT_ROOT);

        environment()->setMutable(true);

        $this->fs = FileSystemFactory::get();
    }

    public function tearDown(): void
    {
        request()->setMatchedRoute(null);
        request()->flush();

        AppFactory::destroy(AppType::WEB);

        if (Di::isRegistered(Config::class)) {
            config()->flush();
        }
        if (Di::isRegistered(Debugger::class)) {
            Di::get(Debugger::class)->resetStore();
        }

        $this->clearAppContext();
    }

    protected function createContext(string $mode = AppType::WEB): AppContext
    {
        $context = new AppContext($mode, PROJECT_ROOT, new DiContainer());
        App::setContext($context);

        return $context;
    }

    protected function clearAppContext(): void
    {
        $prop = new ReflectionProperty(App::class, 'context');
        $prop->setAccessible(true);
        $prop->setValue(null, null);
    }

    protected function setPrivateProperty($object, $property, $value): void
    {
        $reflection = new ReflectionClass($object);
        $property = $reflection->getProperty($property);
        $property->setAccessible(true);

        if (is_string($object)) {
            $property->setValue(null, $value);
        } else {
            $property->setValue($object, $value);
        }
    }

    protected function getPrivateProperty($object, $property)
    {
        $reflection = new ReflectionClass($object);
        $property = $reflection->getProperty($property);
        $property->setAccessible(true);
        return $property->getValue($object);
    }

    protected function createFile(string $filePath, string $content)
    {
        $this->fs->put($filePath, $content);
    }

    protected function removeFile(string $filePath)
    {
        if ($this->fs->exists($filePath)) {
            $this->fs->remove($filePath);
        }
    }

    protected function testRequest(
        string $uri,
        string $method = 'GET',
        array $body = [],
        array $headers = []
    ) {
        request()->create($method, $uri, $body, $headers);

        $route = new Route(
            [$method],
            $uri,
            'TestController',
            'testAction'
        );
        $route->module('Test');

        $matchedRoute = new MatchedRoute($route, []);
        request()->setMatchedRoute($matchedRoute);
    }
}
