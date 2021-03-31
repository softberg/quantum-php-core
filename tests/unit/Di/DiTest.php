<?php

namespace Quantum\Controllers {

    use Quantum\Mvc\QtController;
    use Quantum\Factory\ViewFactory;
    use Quantum\Http\Request;
    use Quantum\Http\Response;

    class TestDiController extends QtController
    {

        public function index(Request $request, Response $response, ViewFactory $view)
        {
            // method body
        }

    }

}

namespace Quantum\Test\Unit {

    use PHPUnit\Framework\TestCase;
    use Quantum\Libraries\Storage\FileSystem;
    use Quantum\Controllers\TestDiController;
    use Quantum\Exceptions\DiException;
    use Quantum\Factory\ServiceFactory;
    use Quantum\Factory\ViewFactory;
    use Quantum\Http\Response;
    use Quantum\Http\Request;
    use Quantum\Loader\Loader;
    use Quantum\Di\Di;

    class DiTest extends TestCase
    {

        public function setUp(): void
        {
            $loader = new Loader(new FileSystem);

            $loader->loadDir(dirname(__DIR__, 3) . DS . 'src' . DS . 'Helpers' . DS . 'functions');

            Di::loadDefinitions();
        }

        public function testGetDependency()
        {
            $this->assertInstanceOf(Loader::class, Di::get(Loader::class));

            $this->assertNotInstanceOf(FileSystem::class, Di::get(Loader::class));

            $this->expectException(DiException::class);

            $this->expectExceptionMessage('Dependency `' . DiException::class . '` not defined');

            Di::get(DiException::class);
        }

        public function testAutowire()
        {
            $params = Di::autowire(TestDiController::class . ':' . 'index');

            $this->assertInstanceOf(Request::class, $params[0]);

            $this->assertInstanceOf(Response::class, $params[1]);

            $this->assertInstanceOf(ViewFactory::class, $params[2]);
            
            $callback = function(Request $request, Response $response, ServiceFactory $service){
                // function body
            };
            
            $params = Di::autowire($callback);
            
            $this->assertInstanceOf(Request::class, $params[0]);

            $this->assertInstanceOf(Response::class, $params[1]);

            $this->assertInstanceOf(ServiceFactory::class, $params[2]);
            
            
        }

    }

}