<?php

namespace Quantum\Controllers {

    use Quantum\Mvc\QtController;

    class TestController extends QtController
    {
        
    }

}

namespace Quantum\Test\Unit {

    use Mockery;
    use PHPUnit\Framework\TestCase;
    use Quantum\Exceptions\ControllerException;
    use Quantum\Mvc\QtController;
    use Quantum\Controllers\TestController;
    use Quantum\Libraries\Storage\FileSystem;
    use Quantum\Loader\Loader;

    /**
     * @runTestsInSeparateProcesses
     * @preserveGlobalState disabled
     */
    class QtControllerTest extends TestCase
    {

        public function setUp(): void
        {
            $loader = new Loader(new FileSystem);

            $loader->loadDir(dirname(__DIR__, 3) . DS . 'src' . DS . 'Helpers' . DS . 'functions');
        }

        public function testGetInstance()
        {
            $this->assertInstanceOf('Quantum\Mvc\QtController', QtController::getInstance());

            $this->assertInstanceOf('Quantum\Mvc\QtController', new TestController());
        }

        public function testMissingeMethods()
        {
            $this->expectException(ControllerException::class);

            $this->expectExceptionMessage('The method `undefinedMethod` is not defined');

            $controller = QtController::getInstance();

            $controller->undefinedMethod();
        }

    }

}
