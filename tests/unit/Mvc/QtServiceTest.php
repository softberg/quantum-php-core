<?php

namespace Quantum\Services {


    use Quantum\Mvc\QtService;

    class TestingService extends QtService
    {

    }

}

namespace Quantum\Tests\Mvc {

    use PHPUnit\Framework\TestCase;
    use Quantum\Exceptions\ServiceException;
    use Quantum\Services\TestingService;
    use Quantum\Factory\ServiceFactory;
    use Quantum\Di\Di;
    use Quantum\App;

    /**
     * @runTestsInSeparateProcesses
     * @preserveGlobalState disabled
     */
    class QtServiceTest extends TestCase
    {

        public function setUp(): void
        {
            App::loadCoreFunctions(dirname(__DIR__, 3) . DS . 'src' . DS . 'Helpers');

            App::setBaseDir(dirname(__DIR__) . DS . '_root');

            Di::loadDefinitions();
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