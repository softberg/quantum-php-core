<?php

namespace Quantum\Tests\Unit\Router;

use Quantum\Exceptions\StopExecutionException;
use Quantum\Router\Exceptions\RouteException;
use Quantum\Tests\Unit\AppTestCase;
use Quantum\Http\Response;
use Quantum\Router\Router;
use Quantum\Http\Request;

class RouterTest extends AppTestCase
{

    private $request;
    private $router;

    public function setUp(): void
    {
        parent::setUp();

        Response::init();

        $this->request = new Request();

        $this->router = new Router($this->request);

        $reflectionClass = new \ReflectionClass(Router::class);

        $reflectionProperty = $reflectionClass->getProperty('currentRoute');

        $reflectionProperty->setAccessible(true);

        $reflectionProperty->setValue(null);
    }

    public function testSetGetRoutes()
    {
        Router::setRoutes([
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

        $this->assertNotEmpty(Router::getRoutes());

        $this->assertIsArray(Router::getRoutes());
    }

    public function testFindRoute()
    {
        $this->assertNull($this->router->getCurrentRoute());

        Router::setRoutes([
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
        Router::setRoutes([
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
        Router::setRoutes([
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
        Router::setRoutes([
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

    public function testRestfulRoutes()
    {
        Router::setRoutes([
            [
                "route" => "api-task",
                "method" => "POST",
                "controller" => "TaskController",
                "action" => "create",
                "module" => "Api",
            ],
            [
                "route" => "api-task",
                "method" => "GET",
                "controller" => "TaskController",
                "action" => "show",
                "module" => "Api",
            ]
        ]);

        $this->request->create('GET', 'http://testdomain.com/api-task');

        $this->router->findRoute();

        $this->assertEquals('GET', route_method());

        $this->assertEquals('show', current_action());

        $request = new Request();

        $router = new Router($request);

        $request->create('POST', 'http://testdomain.com/api-task');

        $router->findRoute();

        $this->assertEquals('POST', route_method());

        $this->assertEquals('create', current_action());
    }

    public function testRouteIncorrectMethod()
    {
        Router::setRoutes([
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

        $this->expectExceptionMessage('incorrect_method');

        $this->router->findRoute();
    }

    public function testRepetitiveRoutesWithSameMethod()
    {
        Router::setRoutes([
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

        $this->expectExceptionMessage('repetitive_route_same_method');

        $this->router->findRoute();
    }

    public function testRepetitiveRoutesInDifferentModules()
    {
        Router::setRoutes([
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

        $this->expectExceptionMessage('repetitive_route_different_modules');

        $this->router->findRoute();
    }

    public function testRouteNotFound()
    {
        $this->request->create('GET', 'http://testdomain.com/something');

        $this->expectException(StopExecutionException::class);

        $this->expectExceptionMessage('execution_terminated');

        $this->router->findRoute();
    }
}