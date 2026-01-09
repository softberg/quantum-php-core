<?php

namespace Quantum\Controllers {

    use Quantum\Router\RouteController;

    class SomeController extends RouteController
    {
        // Controller body
    }
}

namespace Quantum\Tests\Unit\Router {

    use Quantum\Router\Exceptions\RouteControllerException;
    use Quantum\Controllers\SomeController;
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
            $this->expectException(RouteControllerException::class);

            $this->expectExceptionMessage('Action `undefinedAction` not defined');

            $controller = new SomeController();

            $controller->undefinedAction();
        }

    }
}
