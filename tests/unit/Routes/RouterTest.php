<?php

namespace Quantum\Test\Unit {

    use Quantum\Di\Di;
    use Quantum\Exceptions\RouteException;
    use PHPUnit\Framework\TestCase;
    use Quantum\Exceptions\StopExecutionException;
    use Quantum\Http\Response;
    use Quantum\Routes\Router;
    use Quantum\Http\Request;
    use Quantum\App;
    use Mockery;

    class RouterTest extends TestCase
    {

        private $request;
        private $router;

        public function setUp(): void
        {
            App::loadCoreFunctions(dirname(__DIR__, 3) . DS . 'src' . DS . 'Helpers');

            App::setBaseDir(dirname(__DIR__) . DS . '_root');

            Di::loadDefinitions();

            $this->request = new Request();

            $this->router = new Router($this->request, new Response());

            $reflectionClass = new \ReflectionClass(Router::class);

            $reflectionProperty = $reflectionClass->getProperty('currentRoute');

            $reflectionProperty->setAccessible(true);

            $reflectionProperty->setValue(null);
        }

        public function testSetGetRoutes()
        {
            $this->assertEmpty($this->router->getRoutes());

            $this->router->setRoutes([
                [
                    "route" => "[:alpha:2]?",
                    "method" => "GET",
                    "controller" => "MainController",
                    "action" => "index",
                    "module" => "Web"
                ],
                [
                    "route" => "[:alpha:2]?/about",
                    "method" => "GET",
                    "controller" => "MainController",
                    "action" => "about",
                    "module" => "Web"
                ]
            ]);

            $this->assertNotEmpty($this->router->getRoutes());

            $this->assertIsArray($this->router->getRoutes());
        }

        public function testFindRoute()
        {
            $this->assertNull($this->router->getCurrentRoute());

            $this->router->setRoutes([
                [
                    "route" => "api-signin",
                    "method" => "POST",
                    "controller" => "AuthController",
                    "action" => "signin",
                    "module" => "Api",
                ],
                [
                    "route" => "api-signout-test",
                    "method" => "GET",
                    "controller" => "AuthController",
                    "action" => "signout",
                    "module" => "Api",
                    "middlewares" => [
                        0 => "guest"
                    ]
                ]
            ]);

            $this->request->create('POST', 'http://testdomain.com/api-signin');

            $this->router->findRoute();

            $this->assertNotNull($this->router->getCurrentRoute());

            $this->assertIsArray($this->router->getCurrentRoute());
        }

        public function testRouteIncorrectMethod()
        {
            $this->router->setRoutes([
                [
                    "route" => "api-signin",
                    "method" => "POST",
                    "controller" => "AuthController",
                    "action" => "signin",
                    "module" => "Api",
                ]
            ]);

            $this->request->create('GET', 'http://testdomain.com/api-signin');

            $this->expectException(RouteException::class);

            $this->expectExceptionMessage('Incorrect Method `GET`');

            $this->router->findRoute();
        }

        public function testRepetitiveRoutesWithSameMethod()
        {
            $this->router->setRoutes([
                [
                    "route" => "api-signin",
                    "method" => "POST",
                    "controller" => "AuthController",
                    "action" => "signin",
                    "module" => "Api",
                ],
                [
                    "route" => "api-signin",
                    "method" => "POST",
                    "controller" => "AuthController",
                    "action" => "signin",
                    "module" => "Api",
                ]
            ]);

            $this->request->create('POST', 'http://testdomain.com/api-signin');

            $this->expectException(RouteException::class);

            $this->expectExceptionMessage('Repetitive Routes with same method `POST`');

            $this->router->findRoute();
        }

        public function testRepetitiveRoutesInDifferentModules()
        {
            $this->router->setRoutes([
                [
                    "route" => "api-signin",
                    "method" => "POST",
                    "controller" => "AuthController",
                    "action" => "signin",
                    "module" => "Api",
                ],
                [
                    "route" => "api-signin",
                    "method" => "PUT",
                    "controller" => "AuthController",
                    "action" => "signin",
                    "module" => "Web",
                ]
            ]);

            $this->request->create('POST', 'http://testdomain.com/api-signin');

            $this->expectException(RouteException::class);

            $this->expectExceptionMessage('Repetitive Routes in different modules');

            $this->router->findRoute();
        }

        public function testRouteNotFound()
        {
            $this->request->create('GET', 'http://testdomain.com/something');

            $this->expectException(StopExecutionException::class);

            $this->expectExceptionMessage(StopExecutionException::EXECUTION_TERMINATED);

            $this->router->findRoute();
        }

    }

}
    