<?php

namespace Quantum\Tests\Router;

use Quantum\Exceptions\StopExecutionException;
use Quantum\Exceptions\RouteException;
use PHPUnit\Framework\TestCase;
use Quantum\Http\Response;
use Quantum\Router\Router;
use Quantum\Http\Request;
use Quantum\Di\Di;
use Quantum\App;

class RouterTest extends TestCase
{

    private $request;
    private $router;

    public function setUp(): void
    {
        App::loadCoreFunctions(dirname(__DIR__, 3) . DS . 'src' . DS . 'Helpers');

        App::setBaseDir(dirname(__DIR__) . DS . '_root');

        Di::loadDefinitions();

        Response::init();

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
                "route" => "auth/api-signin",
                "method" => "POST",
                "controller" => "AuthController",
                "action" => "signin",
                "module" => "Api",
            ],
            [
                "route" => "auth/api-signout-test",
                "method" => "GET",
                "controller" => "AuthController",
                "action" => "signout",
                "module" => "Api",
                "middlewares" => [
                    0 => "guest"
                ]
            ]
        ]);

        $this->request->create('POST', 'http://testdomain.com/auth/api-signin');

        $this->router->findRoute();

        $this->assertNotNull($this->router->getCurrentRoute());

        $this->assertEquals('AuthController', current_controller());

        $this->assertEquals('signin', current_action());

        $this->assertEmpty(route_params());
    }

    public function testFindRouteWithParams()
    {
        $this->router->setRoutes([
            [
                "route" => "[:alpha:2]/my-posts/amend/[:any]",
                "method" => "POST",
                "controller" => "PostController",
                "action" => "amendPost",
                "module" => "Web",
            ]
        ]);

        $this->request->create('POST', 'http://testdomain.com/en/my-posts/amend/5e538098-1095-3976-b05f-29a0bb2a799f');

        $this->router->findRoute();

        $params = route_params();

        $this->assertIsArray($params);

        $this->assertEquals('en', $params[0]['value']);

        $this->assertEquals('5e538098-1095-3976-b05f-29a0bb2a799f', $params[1]['value']);
    }

    public function testFindRouteWithOptionalParams()
    {
        $this->router->setRoutes([
            [
                "route" => "[:any]?/my-posts/amend/[:any]/[:num]?",
                "method" => "POST",
                "controller" => "PostController",
                "action" => "amendPost",
                "module" => "Web",
            ]
        ]);

        $this->request->create('POST', 'http://testdomain.com/my-posts/amend/5e538098-1095-3976-b05f-29a0bb2a799f');

        $this->router->findRoute();

        $params = route_params();

        $this->assertNull($params[0]['value']);

        $this->assertEquals('5e538098-1095-3976-b05f-29a0bb2a799f', $params[1]['value']);

        $this->assertNull($params[2]['value']);
    }

    public function testFindRouteWithNamedParams()
    {
        $this->router->setRoutes([
            [
                "route" => "[lang=:alpha:2]?/my-posts/amend/[postId=:any]/[ref=:num]?",
                "method" => "POST",
                "controller" => "PostController",
                "action" => "amendPost",
                "module" => "Web",
            ]
        ]);

        $this->request->create('POST', 'http://testdomain.com/my-posts/amend/5e538098-1095-3976-b05f-29a0bb2a799f/523');

        $this->router->findRoute();

        $this->assertNull(route_param('lang'));

        $this->assertEquals('5e538098-1095-3976-b05f-29a0bb2a799f', route_param('postId'));

        $this->assertEquals('523', route_param('ref'));
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
