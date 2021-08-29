<?php

namespace Quantum\Services {


    use Quantum\Mvc\QtService;

    class TestingService extends QtService
    {

    }

}

namespace Quantum\Test\Unit {

    use PHPUnit\Framework\TestCase;
    use Quantum\Exceptions\ServiceException;
    use Quantum\Factory\ServiceFactory;
    use Quantum\Libraries\Storage\FileSystem;
    use Quantum\Loader\Loader;
    use Quantum\Services\TestingService;

    /**
     * @runTestsInSeparateProcesses
     * @preserveGlobalState disabled
     */
    class QtServiceTest extends TestCase
    {

        public function setUp(): void
        {
            $loader = new Loader(new FileSystem);

            $loader->loadDir(dirname(__DIR__, 3) . DS . 'src' . DS . 'Helpers' . DS . 'functions');
        }

        /**
         * @runInSeparateProcess
         */
        public function testMissingeMethods()
        {
            $this->expectException(ServiceException::class);

            $this->expectExceptionMessage('The method `undefinedMethod` is not defined');

            $service = (new ServiceFactory)->get(TestingService::class);

            $service->undefinedMethod();
        }

    }
}