<?php

namespace Quantum\Tests\Helpers;

use Quantum\Tests\AppTestCase;
use Quantum\Router\Router;
use Quantum\Http\Response;
use Quantum\Http\Request;

class MvcHelperTest extends AppTestCase
{

    private $router;
    private $request;

    public function setUp(): void
    {
        parent::setUp();

        $this->request = new Request();

        $this->router = new Router($this->request, new Response());
    }

    public function testMvcHelpers()
    {
        Router::setRoutes([
            [
                "route" => "signin",
                "method" => "POST",
                "controller" => "SomeController",
                "action" => "signin",
                "module" => "Test",
                "middlewares" => ["guest", "anonymous"],
                'prefix' => 'api'
            ],
            [
                "route" => "user/[id=:num]",
                "method" => "GET",
                "controller" => "SomeController",
                "action" => "signout",
                "module" => "Test",
                "middlewares" => ["user"],
                'name' => 'user',
                'prefix' => 'api'
            ]
        ]);

        $this->request->create('POST', 'http://testdomain.com/signin');

        $this->router->findRoute();

        $middlewares = current_middlewares();

        $this->assertIsArray($middlewares);

        $this->assertEquals('guest', $middlewares[0]);

        $this->assertEquals('anonymous', $middlewares[1]);

        $this->assertEquals('Test', current_module());

        $this->assertEquals('SomeController', current_controller());

        $this->assertEquals('signin', current_action());

        $this->assertEquals('signin', current_route());

        $this->assertEmpty(route_params());

        $this->request->create('GET', 'http://testdomain.com/user/12');

        $this->router->resetRoutes();

        $this->router->findRoute();

        $this->assertNotEmpty(route_params());

        $this->assertEquals(12, route_param('id'));

        $this->assertEquals('(\/)?user(\/)(?<id>[0-9]+)', route_pattern());

        $this->assertEquals('GET', route_method());

        $this->assertEquals('user/12', route_uri());

        $this->assertEquals('user', route_name());

        $this->assertEquals('api', route_prefix());
    }

    public function testMvcRouteCallback()
    {
        Router::setRoutes([
            [
                "route" => "home",
                "method" => "GET",
                "callback" => function (Response $response) {},
                "module" => "Test",
            ]
        ]);

        $this->request->create('GET', '/home');

        $this->router->findRoute();

        $this->assertIsCallable(route_callback());
    }

    public function testMvcFindRouteByName()
    {
        $this->assertNull(find_route_by_name('user', 'Test'));

        Router::setRoutes([
            [
                "route" => "api-user/[id=:num]",
                "method" => "GET",
                "controller" => "SomeController",
                "action" => "signout",
                "module" => "Test",
                "middlewares" => ["user"],
                "name" => "user"
            ]
        ]);

        $this->assertNotNull(find_route_by_name('user', 'Test'));

        $this->assertIsArray(find_route_by_name('user', 'Test'));
    }

    public function testMvcCheckRouteGroupExists()
    {
        $this->assertFalse(route_group_exists('guest', 'Test'));

        Router::setRoutes([
            [
                "route" => "api-user/[id=:num]",
                "method" => "GET",
                "controller" => "SomeController",
                "action" => "signout",
                "module" => "Test",
                "middlewares" => ["user"],
                'group' => 'guest',
                "name" => "user"
            ]
        ]);

        $this->assertTrue(route_group_exists('guest', 'Test'));
    }

}