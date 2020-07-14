<?php

namespace Quantum\Controllers {

    use Quantum\Mvc\QtController;

    class TestController extends QtController {}

}

namespace Quantum\Test\Unit {

    use Mockery;
    use PHPUnit\Framework\TestCase;
    use Quantum\Mvc\QtController;
    use Quantum\Controllers\TestController;

    /**
     * @runTestsInSeparateProcesses
     * @preserveGlobalState disabled
     */
    class QtControllerTest extends TestCase
    {

        public function setUp(): void
        {
            $this->helperMock = Mockery::mock('overload:Quantum\Helpers\Helper');

            $this->helperMock->shouldReceive('_message')->andReturnUsing(function($subject, $params) {
                if (is_array($params)) {
                    return preg_replace_callback('/{%\d+}/', function () use (&$params) {
                        return array_shift($params);
                    }, $subject);
                } else {
                    return preg_replace('/{%\d+}/', $params, $subject);
                }
            });
        }

        public function testGetInstance()
        {
            $this->assertInstanceOf('Quantum\Mvc\QtController', QtController::getInstance());
            
            $this->assertInstanceOf('Quantum\Mvc\QtController', new TestController());
        }

        public function testMissingeMethods()
        {
            $this->expectException(\BadMethodCallException::class);

            $this->expectExceptionMessage('The method `undefinedMethod` is not defined');

            $controller = QtController::getInstance();

            $controller->undefinedMethod();
        }

    }

}
