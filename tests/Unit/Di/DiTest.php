<?php

namespace Quantum\Controllers {

    use Quantum\Service\DummyServiceInterface;
    use Quantum\View\Factories\ViewFactory;
    use Quantum\Http\Request;
    use Quantum\Http\Response;

    class TestDiController
    {
        public function index(Request $request, Response $response, ViewFactory $view): void
        {
            // method body
        }

        public function handleService(DummyServiceInterface $service): DummyServiceInterface
        {
            return $service;
        }
    }
}

namespace Quantum\Service {

    interface DummyServiceInterface
    {
    }

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
    use Quantum\Di\DiContainer;
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

        public function testDiRegisterDependency(): void
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

        public function testDiIsRegistered(): void
        {
            $this->assertFalse(Di::isRegistered(DummyService::class));

            Di::register(DummyService::class);

            $this->assertTrue(Di::isRegistered(DummyService::class));
        }

        public function testDiHasReturnsFalseBeforeResolve(): void
        {
            Di::register(DummyService::class);

            $this->assertFalse(Di::has(DummyService::class));
        }

        public function testDiHasReturnsTrueAfterGet(): void
        {
            Di::register(DummyService::class);

            Di::get(DummyService::class);

            $this->assertTrue(Di::has(DummyService::class));
        }

        public function testDiHasReturnsTrueAfterSet(): void
        {
            $instance = new DummyService();

            Di::set(DummyServiceInterface::class, $instance);

            $this->assertTrue(Di::has(DummyServiceInterface::class));
        }

        public function testDiHasReturnsFalseAfterResetContainer(): void
        {
            Di::register(DummyService::class);

            Di::get(DummyService::class);

            $this->assertTrue(Di::has(DummyService::class));

            Di::resetContainer();

            $this->assertFalse(Di::has(DummyService::class));
        }

        public function testDiHasReturnsFalseAfterReset(): void
        {
            Di::register(DummyService::class);

            Di::get(DummyService::class);

            $this->assertTrue(Di::has(DummyService::class));

            Di::reset();

            $this->assertFalse(Di::has(DummyService::class));
        }

        public function testDiAbstractToConcreteBinding(): void
        {
            Di::register(DummyService::class, DummyServiceInterface::class);

            $instance = Di::get(DummyServiceInterface::class);

            $this->assertInstanceOf(DummyService::class, $instance);
        }

        public function testDiSetBindsInstanceToAbstract(): void
        {
            $instance = new DummyService();

            Di::set(DummyServiceInterface::class, $instance);

            $resolved = Di::get(DummyServiceInterface::class);

            $this->assertSame($instance, $resolved);
            $this->assertInstanceOf(DummyService::class, $resolved);
        }

        public function testDiSetWorksWithoutPriorRegister(): void
        {
            $instance = new DummyService();

            $this->assertFalse(Di::isRegistered(DummyServiceInterface::class));

            Di::set(DummyServiceInterface::class, $instance);

            $this->assertTrue(Di::isRegistered(DummyServiceInterface::class));

            $this->assertSame($instance, Di::get(DummyServiceInterface::class));
        }

        public function testDiSetRejectsWrongInstanceType(): void
        {
            $this->expectException(DiException::class);
            $this->expectExceptionMessage(
                'The dependency `' . DummyServiceInterface::class . '` is not valid abstract class.'
            );

            Di::set(DummyServiceInterface::class, new \stdClass());
        }

        public function testDiSetRejectsInvalidAbstract(): void
        {
            $this->expectException(DiException::class);
            $this->expectExceptionMessage(
                'The dependency `NonExistentInterface` is not valid abstract class.'
            );

            Di::set('NonExistentInterface', new DummyService());
        }

        public function testDiSetRejectsWhenAlreadyResolved(): void
        {
            Di::register(DummyService::class);

            Di::get(DummyService::class);

            $this->expectException(DiException::class);
            $this->expectExceptionMessage(
                'The dependency `' . DummyService::class . '` is already registered.'
            );

            Di::set(DummyService::class, new DummyService());
        }

        public function testDiSetOverridesRegisteredButNotResolved(): void
        {
            Di::register(DummyService::class, DummyServiceInterface::class);

            $instance = new DummyService();

            Di::set(DummyServiceInterface::class, $instance);

            $resolved = Di::get(DummyServiceInterface::class);

            $this->assertSame($instance, $resolved);
        }

        public function testDiGetCoreDependencies(): void
        {
            $this->assertInstanceOf(Loader::class, Di::get(Loader::class));

            $this->assertInstanceOf(Request::class, Di::get(Request::class));

            $this->assertInstanceOf(Response::class, Di::get(Response::class));
        }

        public function testDiGetAutoRegistersInstantiableClass(): void
        {
            $this->assertFalse(Di::isRegistered(DiException::class));

            $instance = Di::get(DiException::class);

            $this->assertInstanceOf(DiException::class, $instance);
            $this->assertTrue(Di::isRegistered(DiException::class));
        }

        public function testDiGetThrowsForNonInstantiableClass(): void
        {
            $this->expectException(DiException::class);

            $this->expectExceptionMessage('The dependency `Quantum\Service\DummyServiceInterface` is not registered.');

            Di::get(DummyServiceInterface::class);
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

        public function testDiGetReturnsSingleton(): void
        {
            Di::register(DummyService::class);

            $instance1 = Di::get(DummyService::class);

            $instance2 = Di::get(DummyService::class);

            $this->assertInstanceOf(DummyService::class, $instance1);

            $this->assertSame($instance1, $instance2);
        }

        public function testDiCreateReturnsNewInstance(): void
        {
            Di::register(DummyService::class);

            $instance1 = Di::create(DummyService::class);

            $instance2 = Di::create(DummyService::class);

            $this->assertInstanceOf(DummyService::class, $instance1);

            $this->assertNotSame($instance1, $instance2);
        }

        public function testDiAutowire(): void
        {
            $params = Di::autowire([new TestDiController(), 'index']);

            $this->assertInstanceOf(Request::class, $params[0]);

            $this->assertInstanceOf(Response::class, $params[1]);

            $this->assertInstanceOf(ViewFactory::class, $params[2]);

            $callback = function (Request $request, Response $response): void {
                // function body
            };

            $params = Di::autowire($callback);

            $this->assertInstanceOf(Request::class, $params[0]);

            $this->assertInstanceOf(Response::class, $params[1]);
        }

        public function testDiAutowireWithAbstract(): void
        {
            Di::register(DummyService::class, DummyServiceInterface::class);

            $controller = new TestDiController();

            $params = Di::autowire([$controller, 'handleService']);

            $this->assertInstanceOf(DummyServiceInterface::class, $params[0]);

            $this->assertInstanceOf(DummyService::class, $params[0]);
        }

        public function testDiReset(): void
        {
            Di::register(DummyService::class);

            Di::get(DummyService::class);

            $this->assertTrue(Di::isRegistered(DummyService::class));

            Di::reset();

            $this->assertFalse(Di::isRegistered(DummyService::class));
            $this->assertFalse(Di::has(DummyService::class));
        }

        public function testDiResetContainer(): void
        {
            Di::register(DummyService::class);

            Di::get(DummyService::class);

            $this->assertTrue(Di::isRegistered(DummyService::class));
            $this->assertTrue(Di::has(DummyService::class));

            Di::resetContainer();

            $this->assertTrue(Di::isRegistered(DummyService::class));
            $this->assertFalse(Di::has(DummyService::class));
        }

        public function testDiSetAndGetCurrent(): void
        {
            $container = new DiContainer();
            Di::setCurrent($container);

            $this->assertSame($container, Di::getCurrent());
        }

        public function testDiGetCurrentCreatesDefaultContainer(): void
        {
            $currentProp = new ReflectionProperty(Di::class, 'current');
            $currentProp->setAccessible(true);
            $currentProp->setValue(null, null);

            $container = Di::getCurrent();

            $this->assertInstanceOf(DiContainer::class, $container);
        }

        public function testDiResetCreatesNewContainer(): void
        {
            $containerBefore = Di::getCurrent();

            Di::reset();

            $containerAfter = Di::getCurrent();

            $this->assertNotSame($containerBefore, $containerAfter);
        }

        public function testDiContainerIsolation(): void
        {
            $container1 = new DiContainer();
            $container1->register(DummyService::class);
            $container1->get(DummyService::class);

            $container2 = new DiContainer();

            $this->assertTrue($container1->isRegistered(DummyService::class));
            $this->assertTrue($container1->has(DummyService::class));

            $this->assertFalse($container2->isRegistered(DummyService::class));
            $this->assertFalse($container2->has(DummyService::class));
        }

        public function testDiContainerInstanceMethods(): void
        {
            $container = new DiContainer();

            $container->register(DummyService::class);

            $this->assertTrue($container->isRegistered(DummyService::class));
            $this->assertFalse($container->has(DummyService::class));

            $instance = $container->get(DummyService::class);

            $this->assertInstanceOf(DummyService::class, $instance);
            $this->assertTrue($container->has(DummyService::class));
            $this->assertSame($instance, $container->get(DummyService::class));
        }

        public function testDiContainerResetClearsAll(): void
        {
            $container = new DiContainer();

            $container->register(DummyService::class);
            $container->get(DummyService::class);

            $container->reset();

            $this->assertFalse($container->isRegistered(DummyService::class));
            $this->assertFalse($container->has(DummyService::class));
        }

        public function testDiContainerResetContainerKeepsDependencies(): void
        {
            $container = new DiContainer();

            $container->register(DummyService::class);
            $container->get(DummyService::class);

            $container->resetContainer();

            $this->assertTrue($container->isRegistered(DummyService::class));
            $this->assertFalse($container->has(DummyService::class));
        }
    }
}
