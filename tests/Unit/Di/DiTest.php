<?php

namespace Quantum\Controllers {

    use Quantum\Factory\ViewFactory;
    use Quantum\Mvc\QtController;
    use Quantum\Http\Response;
    use Quantum\Http\Request;

    class TestDiController extends QtController
    {
        public function index(Request $request, Response $response, ViewFactory $view)
        {
            // method body
        }
    }
}

namespace Quantum\Tests\Unit\Di {

    use Quantum\Controllers\TestDiController;
    use Quantum\Libraries\Storage\FileSystem;
    use Quantum\Di\Exceptions\DiException;
    use Quantum\Tests\Unit\AppTestCase;
    use Quantum\Factory\ViewFactory;
    use Quantum\Loader\Loader;
    use Quantum\Http\Response;
    use Quantum\Http\Request;
    use Quantum\Loader\Setup;
    use Quantum\Di\Di;

    class DiTest extends AppTestCase
    {

        public function setUp(): void
        {
            parent::setUp();
        }

        public function testAddDependency()
        {
            Di::add(Setup::class);

            $this->assertInstanceOf(Setup::class, Di::get(Setup::class));
        }

        public function testGetDependency()
        {
            $this->assertInstanceOf(Loader::class, Di::get(Loader::class));

            $this->assertNotInstanceOf(FileSystem::class, Di::get(Loader::class));

            $this->expectException(DiException::class);

            $this->expectExceptionMessage('dependency_not_found');

            Di::get(DiException::class);
        }

        public function testAutowire()
        {
            $params = Di::autowire([new TestDiController, 'index']);

            $this->assertInstanceOf(Request::class, $params[0]);

            $this->assertInstanceOf(Response::class, $params[1]);

            $this->assertInstanceOf(ViewFactory::class, $params[2]);

            $callback = function (Request $request, Response $response) {
                // function body
            };

            $params = Di::autowire($callback);

            $this->assertInstanceOf(Request::class, $params[0]);

            $this->assertInstanceOf(Response::class, $params[1]);
        }
    }
}