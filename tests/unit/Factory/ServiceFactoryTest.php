<?php

namespace Quantum\Factory {

    function _message($message, $args)
    {
        return preg_replace('/{%\d+}/', $args, $message);
    }

}

namespace Quantum\Mvc {

    use Quantum\Factory\ServiceFactory;

    function _message($message, $args)
    {
        return $message;
    }

    function get_caller_class()
    {
        return ServiceFactory::class;
    }

}

namespace Quantum\Services {

    use Quantum\Mvc\Qt_Service;

    class TestService extends Qt_Service
    {

        public function __init()
        {
            echo '*';
        }

    }

}

namespace Quantum\Test\Unit {

    use PHPUnit\Framework\TestCase;
    use Quantum\Factory\ServiceFactory;
    use Quantum\Services\TestService;

    class ServiceFactoryTest extends TestCase
    {

        private $serviceFactory;

        public function setUp(): void
        {
            $this->serviceFactory = new ServiceFactory();
        }

        public function tearDown(): void
        {
            $reflectionProperty = new \ReflectionProperty(ServiceFactory::class, 'initialized');
            $reflectionProperty->setAccessible(true);
            $reflectionProperty->setValue(ServiceFactory::class, []);
        }

        public function testServiceGet()
        {
            $service = $this->serviceFactory->get(TestService::class);

            $this->assertInstanceOf('Quantum\Mvc\Qt_Service', $service);

            $this->assertInstanceOf('Quantum\Services\TestService', $service);
        }

        public function testServiceGetAndInit()
        {
            /* Calling 3 tiems to verify __init() method works once */

            $this->serviceFactory->get(TestService::class);

            $this->serviceFactory->get(TestService::class);

            $this->serviceFactory->get(TestService::class);

            $this->expectOutputString('*');
        }

        public function testServiceProxy()
        {
            $service = $this->serviceFactory->proxy(TestService::class);

            $this->assertInstanceOf('Quantum\Factory\ServiceFactory', $service);
        }

        public function testServiceProxyAndInit()
        {
            /* Calling 3 tiems to verify __init() method works once */

            $this->serviceFactory->proxy(TestService::class);

            $this->serviceFactory->proxy(TestService::class);

            $this->serviceFactory->proxy(TestService::class);

            $this->expectOutputString('*');
        }

        public function testServiceCreate()
        {
            $service = $this->serviceFactory->create(TestService::class);

            $this->assertInstanceOf('Quantum\Mvc\Qt_Service', $service);

            $this->assertInstanceOf('Quantum\Services\TestService', $service);
        }

        public function testServiceCreateAndInit()
        {
            /* Calling 3 tiems to verify __init() method works each time */

            $this->serviceFactory->create(TestService::class);

            $this->serviceFactory->create(TestService::class);

            $this->serviceFactory->create(TestService::class);

            $this->expectOutputString('***');
        }

    }

}
