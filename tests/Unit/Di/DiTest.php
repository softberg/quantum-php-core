<?php

namespace Quantum\Controllers {

    use Quantum\Service\DummyServiceInterface;
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

        public function handleService(DummyServiceInterface $service)
        {
            return $service;
        }
    }
}

namespace Quantum\Service {

    interface DummyServiceInterface {}

    class DummyService extends QtService implements DummyServiceInterface
    {

    }

    class CircularDependencyA
    {
        public function __construct(CircularDependencyB $b)
        {
        }
    }

    class CircularDependencyB
    {
        public function __construct(CircularDependencyA $a)
        {
        }
    }
}

namespace Quantum\Tests\Unit\Di {

    use Quantum\Service\DummyServiceInterface;
    use Quantum\Controllers\TestDiController;
    use Quantum\Service\CircularDependencyA;
    use Quantum\Service\CircularDependencyB;
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

        public function testDiAttemptingToRegisterAlreadyRegisteredDependency(): void
        {
            $this->expectException(DiException::class);

            $this->expectExceptionMessage('The dependency `Quantum\Loader\Setup` is already registered.');

            Di::register(Setup::class);
            Di::register(Setup::class);
        }

        public function testDiAttemptingToRegisterNonExistentClass(): void
        {
            $this->expectException(DiException::class);
            $this->expectExceptionMessage('The dependency `NonExistentClass` is not instantiable.');

            Di::register('NonExistentClass');
        }

        public function testDiAttemptingToRegisterNonExistentAbstract(): void
        {
            $this->expectException(DiException::class);
            $this->expectExceptionMessage('The dependency `NonExistentInterface` is not valid abstract class.');

            Di::register(DummyService::class, 'NonExistentInterface');
        }

        public function testDiIsRegistered()
        {
            $this->assertFalse(Di::isRegistered(DummyService::class));

            Di::register(DummyService::class);

            $this->assertTrue(Di::isRegistered(DummyService::class));
        }

        public function testDiAbstractToConcreteBinding()
        {
            Di::register(DummyService::class, DummyServiceInterface::class);

            $instance = Di::get(DummyServiceInterface::class);

            $this->assertInstanceOf(DummyService::class, $instance);
        }

        public function testDiGetCoreDependencies()
        {
            $this->assertInstanceOf(Loader::class, Di::get(Loader::class));

            $this->assertInstanceOf(Request::class, Di::get(Request::class));

            $this->assertInstanceOf(Response::class, Di::get(Response::class));
        }

        public function testDiAttemptingToGetNotRegisteredDependency()
        {
            $this->assertInstanceOf(Loader::class, Di::get(Loader::class));

            $this->expectException(DiException::class);

            $this->expectExceptionMessage('The dependency `Quantum\Di\Exceptions\DiException` is not registered.');

            Di::get(DiException::class);
        }

        public function testDiCircularDependencyDetectedAtResolve(): void
        {
            $this->expectException(DiException::class);

            $this->expectExceptionMessage(
                'Circular dependency detected: `' . CircularDependencyA::class .
                ' -> ' . CircularDependencyB::class .
                ' -> ' . CircularDependencyA::class . '`'
            );

            Di::register(CircularDependencyA::class);
            Di::register(CircularDependencyB::class);

            Di::create(CircularDependencyA::class);
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

        public function testDiAutowireWithAbstract()
        {
            Di::register(DummyService::class, DummyServiceInterface::class);

            $controller = new TestDiController();

            $params = Di::autowire([$controller, 'handleService']);

            $this->assertInstanceOf(DummyServiceInterface::class, $params[0]);

            $this->assertInstanceOf(DummyService::class, $params[0]);
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
        }
    }
}