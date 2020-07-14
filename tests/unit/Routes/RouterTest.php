<?php

namespace Quantum\Routes {

    function _message($subject, $params)
    {
        if (is_array($params)) {
            return preg_replace_callback('/{%\d+}/', function () use (&$params) {
                return array_shift($params);
            }, $subject);
        } else {
            return preg_replace('/{%\d+}/', $params, $subject);
        }
    }

}

namespace Quantum\Test\Unit {

    use Quantum\Exceptions\ExceptionMessages;
    use Quantum\Exceptions\RouteException;
    use PHPUnit\Framework\TestCase;
    use Quantum\Routes\Router;
    use Quantum\Http\Request;
    use Mockery;

    class RouterTest extends TestCase
    {

        private $request;
        private $response;
        private $router;

        public function setUp(): void
        {
            $this->request = new Request();

            $this->response = Mockery::mock('Quantum\Http\Response');

            $this->router = new Router($this->request, $this->response);

            $hookManager = Mockery::mock('overload:Quantum\Hooks\HookManager');

            $hookManager->shouldReceive('call')->andReturnUsing(function() {
                throw new RouteException(ExceptionMessages::ROUTE_NOT_FOUND);
            });

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

        public function testRepetetiveRoutesWithSameMethod()
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

        public function testRepetetiveRoutesInDifferentMoodules()
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

            $this->expectException(RouteException::class);

            $this->expectExceptionMessage(ExceptionMessages::ROUTE_NOT_FOUND);

            $this->router->findRoute();
        }

    }

}
    