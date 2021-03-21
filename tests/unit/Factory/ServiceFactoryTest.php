<?php

namespace Quantum\Services {

    use Quantum\Mvc\QtService;

    class TestService extends QtService
    {

        public static $count = 0;

        public function __init()
        {
            self::$count++;
        }

        public function hello()
        {
            return 'Hello';
        }

    }

}

namespace Quantum\Test\Unit {

    use PHPUnit\Framework\TestCase;
    use Quantum\Exceptions\ServiceException;
    use Quantum\Factory\ServiceFactory;
    use Quantum\Services\TestService;
    use Quantum\Libraries\Storage\FileSystem;
    use Quantum\Loader\Loader;

    class ServiceFactoryTest extends TestCase
    {

        private $serviceFactory;

        public function setUp(): void
        {
            $loader = new Loader(new FileSystem);

            $loader->loadDir(dirname(__DIR__, 3) . DS . 'src' . DS . 'Helpers' . DS . 'functions');

            $this->serviceFactory = new ServiceFactory();
        }

        public function tearDown(): void
        {
            TestService::$count = 0;
        }

        public function testServiceGetInstance()
        {
            $service = $this->serviceFactory->get(TestService::class);

            $this->assertInstanceOf('Quantum\Mvc\QtService', $service);

            $this->assertInstanceOf('Quantum\Services\TestService', $service);
        }

        public function testServiceGetAndInit()
        {
            /* Calling 3 tiems to verify __init() method works only once */

            $this->serviceFactory->get(TestService::class);

            $this->assertEquals(1, TestService::$count);

            $this->serviceFactory->get(TestService::class);

            $this->assertEquals(1, TestService::$count);

            $this->serviceFactory->get(TestService::class);

            $this->assertEquals(1, TestService::$count);
        }

        public function testServiceCreateInstance()
        {
            $service = $this->serviceFactory->create(TestService::class);

            $this->assertInstanceOf('Quantum\Mvc\QtService', $service);

            $this->assertInstanceOf('Quantum\Services\TestService', $service);
        }

        public function testServiceCreateAndInit()
        {
            /* Calling 3 tiems to verify __init() method works each time */

            $this->serviceFactory->create(TestService::class);

            $this->assertEquals(1, TestService::$count);

            $this->serviceFactory->create(TestService::class);

            $this->assertEquals(2, TestService::$count);

            $this->serviceFactory->create(TestService::class);

            $this->assertEquals(3, TestService::$count);
        }

        public function testServiceMethodCall()
        {
            $this->assertEquals('Hello', $this->serviceFactory->get(TestService::class)->hello());

            $this->assertEquals('Hello', $this->serviceFactory->create(TestService::class)->hello());
        }

        public function testServiceNotFound()
        {
            $this->expectException(ServiceException::class);

            $this->expectExceptionMessage('Service `NonExistentClass` not found');

            $this->serviceFactory->get(\NonExistentClass::class);
        }

    }

}
