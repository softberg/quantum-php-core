<?php

namespace Quantum\Controllers {

    use Quantum\Mvc\QtController;

    class TestController extends QtController
    {
        
    }

}

namespace Quantum\Tests\Mvc {

    use PHPUnit\Framework\TestCase;
    use Quantum\Exceptions\ControllerException;
    use Quantum\Controllers\TestController;
    use Quantum\Di\Di;
    use Quantum\App;

    /**
     * @runTestsInSeparateProcesses
     * @preserveGlobalState disabled
     */
    class QtControllerTest extends TestCase
    {

        public function setUp(): void
        {
            App::loadCoreFunctions(dirname(__DIR__, 3) . DS . 'src' . DS . 'Helpers');

            App::setBaseDir(dirname(__DIR__) . DS . '_root');

            Di::loadDefinitions();
        }

        public function testMissingMethods()
        {
            $this->expectException(ControllerException::class);

            $this->expectExceptionMessage('undefined_method');

            $controller = new TestController();

            $controller->undefinedMethod();
        }

    }

}
