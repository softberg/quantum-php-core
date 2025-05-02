<?php

namespace Quantum\Controllers {

    use Quantum\View\Factories\ViewFactory;
    use Quantum\Router\RouteController;
    use Quantum\Http\Request;
    use Quantum\Http\Response;

    class TestDiController extends RouteController
    {
        public function index(Request $request, Response $response, ViewFactory $view)
        {
            // method body
        }
    }
}

namespace Quantum\Service {

    class DummyService extends QtService
    {

    }
}

namespace Quantum\Tests\Unit\Di {

    use Quantum\Controllers\TestDiController;
    use Quantum\View\Factories\ViewFactory;
    use Quantum\Di\Exceptions\DiException;
    use Quantum\Tests\Unit\AppTestCase;
    use Quantum\Service\DummyService;
    use Quantum\Loader\Loader;
    use Quantum\Http\Response;
    use Quantum\Http\Request;
    use Quantum\Loader\Setup;
    use ReflectionProperty;
    use Quantum\Di\Di;

    class DiTest extends AppTestCase
    {

        public function setUp(): void
        {
            parent::setUp();
        }

        public function testDiRegisterDependency()
        {
            Di::register(Setup::class);

            $this->assertInstanceOf(Setup::class, Di::get(Setup::class));
        }

        public function testDiIsRegistered()
        {
            $this->assertFalse(Di::isRegistered(DummyService::class));

            Di::register(DummyService::class);

            $this->assertTrue(Di::isRegistered(DummyService::class));
        }

        public function testDiGetCoreDependencies()
        {
            $this->assertInstanceOf(Loader::class, Di::get(Loader::class));

            $this->assertInstanceOf(Request::class, Di::get(Request::class));

            $this->assertInstanceOf(Response::class, Di::get(Response::class));
        }

        public function testDiGetNotRegisteredDependency()
        {
            $this->assertInstanceOf(Loader::class, Di::get(Loader::class));

            $this->expectException(DiException::class);

            $this->expectExceptionMessage('dependency_not_registered');

            Di::get(DiException::class);
        }

        public function testDiGetReturnsSingleton()
        {
            Di::register(DummyService::class);

            $instance1 = Di::get(DummyService::class);

            $instance2 = Di::get(DummyService::class);

            $this->assertInstanceOf(DummyService::class, $instance1);

            $this->assertSame($instance1, $instance2);
        }

        public function testDiCreateReturnsNewInstance()
        {
            Di::register(DummyService::class);

            $instance1 = Di::create(DummyService::class);

            $instance2 = Di::create(DummyService::class);

            $this->assertInstanceOf(DummyService::class, $instance1);

            $this->assertNotSame($instance1, $instance2);
        }

        public function testDiAutowire()
        {
            $params = Di::autowire([new TestDiController, 'index']);

            $this->assertInstanceOf(Request::class, $params[0]);

            $this->assertInstanceOf(Response::class, $params[1]);

            $this->assertInstanceOf(ViewFactory::class, $params[2]);

            $callback = function (Request $request, Response $response) {
                // function body
            };

            $params = Di::autowire($callback);

            $this->assertInstanceOf(Request::class, $params[0]);

            $this->assertInstanceOf(Response::class, $params[1]);
        }

        public function testDiReset()
        {
            Di::register(DummyService::class);

            Di::get(DummyService::class);

            $this->assertTrue(Di::isRegistered(DummyService::class));

            Di::reset();

            $dependenciesProperty = new ReflectionProperty(Di::class, 'dependencies');
            $dependenciesProperty->setAccessible(true);

            $containerProperty = new ReflectionProperty(Di::class, 'container');
            $containerProperty->setAccessible(true);

            $this->assertEmpty($dependenciesProperty->getValue());

            $this->assertEmpty($containerProperty->getValue());

            Di::registerDependencies();
        }
    }
}