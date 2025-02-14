<?php

namespace Quantum\Services {


    use Quantum\Mvc\QtService;

    class TestingService extends QtService
    {
        
    }
}

namespace Quantum\Tests\Unit\Mvc {

    use Quantum\Exceptions\ServiceException;
    use Quantum\Services\TestingService;
    use Quantum\Factory\ServiceFactory;
    use Quantum\Tests\Unit\AppTestCase;

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

        /**
         * @runInSeparateProcess
         */
        public function testMissingMethods()
        {
            $this->expectException(ServiceException::class);

            $this->expectExceptionMessage('undefined_method');

            $service = (new ServiceFactory)->get(TestingService::class);

            $service->undefinedMethod();
        }

    }

}