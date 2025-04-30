<?php

namespace Quantum\Controllers {

    use Quantum\Router\RouteController;

    class TestController extends RouteController
    {
        // Controller body
    }
}

namespace Quantum\Tests\Unit\Router {

    use Quantum\Exceptions\ControllerException;
    use Quantum\Controllers\TestController;
    use Quantum\Tests\Unit\AppTestCase;

    /**
     * @runTestsInSeparateProcesses
     * @preserveGlobalState disabled
     */
    class RouteControllerTest extends AppTestCase
    {

        public function setUp(): void
        {
            parent::setUp();
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
