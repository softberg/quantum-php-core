<?php

namespace Quantum\Controllers {

    use Quantum\Mvc\QtController;

    class TestController extends QtController
    {
        // Controller body
    }
}

namespace Quantum\Tests\Unit\Mvc {

    use Quantum\Exceptions\ControllerException;
    use Quantum\Controllers\TestController;
    use Quantum\Tests\Unit\AppTestCase;

    /**
     * @runTestsInSeparateProcesses
     * @preserveGlobalState disabled
     */
    class QtControllerTest extends AppTestCase
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
