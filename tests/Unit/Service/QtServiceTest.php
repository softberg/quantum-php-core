<?php

namespace Quantum\Services {

    use Quantum\Service\QtService;

    class TestingService extends QtService
    {
    }
}

namespace Quantum\Tests\Unit\Service {

    use Quantum\Service\Exceptions\ServiceException;
    use Quantum\Service\Factories\ServiceFactory;
    use Quantum\Services\TestingService;
    use Quantum\Tests\Unit\AppTestCase;
    use Quantum\Service\QtService;

    /**
     * @runTestsInSeparateProcesses
     * @preserveGlobalState disabled
     */
    class QtServiceTest extends AppTestCase
    {
        public function setUp(): void
        {
            parent::setUp();
        }

        public function testMissingMethods()
        {
            $this->expectException(ServiceException::class);

            $this->expectExceptionMessage('The method `undefinedMethod` is not supported for `' . QtService::class . '`');

            $service = (new ServiceFactory())->get(TestingService::class);

            $service->undefinedMethod();
        }

    }

}
