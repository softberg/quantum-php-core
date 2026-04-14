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

    use Quantum\Di\Exceptions\DiException;
    use Quantum\Tests\Unit\AppTestCase;
    use Quantum\Service\DummyService;
    use Quantum\Di\DiContainer;
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

        public function testFacadeDelegatesToCurrentContainer(): void
        {
            $container = new DiContainer();
            Di::setCurrent($container);

            Di::register(DummyService::class);

            $this->assertTrue($container->isRegistered(DummyService::class));

            $instance = Di::get(DummyService::class);

            $this->assertInstanceOf(DummyService::class, $instance);
            $this->assertSame($instance, $container->get(DummyService::class));
        }

        public function testSetAndGetCurrent(): void
        {
            $container = new DiContainer();
            Di::setCurrent($container);

            $this->assertSame($container, Di::getCurrent());
        }

        public function testGetCurrentCreatesDefaultContainer(): void
        {
            $currentProp = new ReflectionProperty(Di::class, 'current');
            $currentProp->setAccessible(true);
            $currentProp->setValue(null, null);

            $container = Di::getCurrent();

            $this->assertInstanceOf(DiContainer::class, $container);
        }

        public function testResetCreatesNewContainer(): void
        {
            $containerBefore = Di::getCurrent();

            Di::reset();

            $containerAfter = Di::getCurrent();

            $this->assertNotSame($containerBefore, $containerAfter);
        }

        public function testResetClearsRegistrationsAndInstances(): void
        {
            Di::register(DummyService::class);
            Di::get(DummyService::class);

            $this->assertTrue(Di::isRegistered(DummyService::class));
            $this->assertTrue(Di::has(DummyService::class));

            Di::reset();

            $this->assertFalse(Di::isRegistered(DummyService::class));
            $this->assertFalse(Di::has(DummyService::class));
        }

        public function testResetContainerKeepsRegistrations(): void
        {
            Di::register(DummyService::class);
            Di::get(DummyService::class);

            $this->assertTrue(Di::isRegistered(DummyService::class));
            $this->assertTrue(Di::has(DummyService::class));

            Di::resetContainer();

            $this->assertTrue(Di::isRegistered(DummyService::class));
            $this->assertFalse(Di::has(DummyService::class));
        }

        public function testFacadeRegisterAndGet(): void
        {
            Di::register(Setup::class);

            $this->assertInstanceOf(Setup::class, Di::get(Setup::class));
        }

        public function testFacadeSet(): void
        {
            $instance = new DummyService();

            Di::set(DummyService::class, $instance);

            $this->assertSame($instance, Di::get(DummyService::class));
        }

        public function testFacadeCreate(): void
        {
            $instance1 = Di::create(DummyService::class);
            $instance2 = Di::create(DummyService::class);

            $this->assertInstanceOf(DummyService::class, $instance1);
            $this->assertNotSame($instance1, $instance2);
        }

        public function testFacadeAutowire(): void
        {
            $callback = function (Request $request, Response $response): void {
            };

            $params = Di::autowire($callback);

            $this->assertInstanceOf(Request::class, $params[0]);
            $this->assertInstanceOf(Response::class, $params[1]);
        }

        public function testCallStaticThrowsForInvalidMethod(): void
        {
            $this->expectException(DiException::class);

            Di::nonExistentMethod();
        }
    }
}
