<?php

namespace Quantum\Tests\Router;

use Quantum\Exceptions\RouteException;
use Quantum\Tests\AppTestCase;
use Quantum\Router\Route;

class RouteTest extends AppTestCase
{

    private $route;

    public function setUp(): void
    {
        parent::setUp();

        $module = [
            'Test' => [
                'prefix' => '',
                'endabled' => true
            ]
        ];

        $this->route = new Route($module);
    }

    public function testCallbackRoute()
    {
        $this->assertEmpty($this->route->getRuntimeRoutes());

        $this->assertEmpty($this->route->getVirtualRoutes()['*']);

        $this->route->post('userinfo', function () {
            info('Save user info');
        });

        $this->route->get('userinfo', function () {
            info('Get user info');
        });

        $this->route->add('userinfo/add', 'GET', function () {
            info('Add detail to user');
        });

        $this->assertIsArray($this->route->getRuntimeRoutes());

        $this->assertIsArray($this->route->getVirtualRoutes());

        $this->assertCount(3, $this->route->getRuntimeRoutes());

        $this->assertCount(3, $this->route->getVirtualRoutes()['*']);

        $virtualRoutes = $this->route->getVirtualRoutes()['*'];

        $this->assertEquals('POST', $virtualRoutes[0]['method']);

        $this->assertEquals('GET', $virtualRoutes[1]['method']);

        $this->assertEquals('GET', $virtualRoutes[2]['method']);

        $this->assertTrue(is_callable($virtualRoutes[0]['callback']));

        $this->assertTrue(is_callable($virtualRoutes[1]['callback']));

        $this->assertTrue(is_callable($virtualRoutes[2]['callback']));
    }

    public function testAddRoute()
    {
        $this->assertEmpty($this->route->getRuntimeRoutes());

        $this->assertEmpty($this->route->getVirtualRoutes()['*']);

        $this->route->add('signin', 'GET', 'AuthController', 'signin');

        $this->assertIsArray($this->route->getRuntimeRoutes());

        $this->assertIsArray($this->route->getVirtualRoutes());

        $this->assertCount(1, $this->route->getRuntimeRoutes());

        $this->assertCount(1, $this->route->getVirtualRoutes()['*']);
    }

    public function testGetRoute()
    {
        $this->assertEmpty($this->route->getRuntimeRoutes());

        $this->assertEmpty($this->route->getVirtualRoutes()['*']);

        $this->route->get('signin', 'AuthController', 'signin');

        $this->assertIsArray($this->route->getRuntimeRoutes());

        $this->assertIsArray($this->route->getVirtualRoutes());

        $this->assertCount(1, $this->route->getRuntimeRoutes());

        $this->assertCount(1, $this->route->getVirtualRoutes()['*']);
    }

    public function testPostRoute()
    {
        $this->assertEmpty($this->route->getRuntimeRoutes());

        $this->assertEmpty($this->route->getVirtualRoutes()['*']);

        $this->route->post('signin', 'AuthController', 'signin');

        $this->assertIsArray($this->route->getRuntimeRoutes());

        $this->assertIsArray($this->route->getVirtualRoutes());

        $this->assertCount(1, $this->route->getRuntimeRoutes());

        $this->assertCount(1, $this->route->getVirtualRoutes()['*']);
    }

    public function testGroupRoute()
    {
        $this->route->group('auth', function ($route) {
            $route->add('dashboard', 'GET', 'AuthController', 'dashboard');
            $route->add('users', 'GET', 'AuthController', 'users');
        });

        $this->assertCount(2, $this->route->getRuntimeRoutes());

        $this->assertEquals('auth', $this->route->getRuntimeRoutes()[0]['group']);

        $this->assertEquals('auth', $this->route->getRuntimeRoutes()[1]['group']);

        $this->assertCount(2, $this->route->getVirtualRoutes()['auth']);
    }

    public function testMiddlewares()
    {
        $this->route->add('signup', 'POST', 'AuthController', 'signup')->middlewares(['signup', 'csrf']);

        $route = current($this->route->getVirtualRoutes()['*']);

        $this->assertCount(2, $route['middlewares']);

        $this->assertEquals('signup', $route['middlewares'][0]);

        $this->assertEquals('csrf', $route['middlewares'][1]);
    }

    public function testGroupMiddlewares()
    {
        $this->route->group('auth', function ($route) {
            $route->add('dashboard', 'GET', 'AuthController', 'dashboard');
            $route->add('user', 'POST', 'AuthController', 'add')->middlewares(['csrf', 'ddos']);
        })->middlewares(['auth', 'owner']);

        $authGroupRoutes = $this->route->getVirtualRoutes()['auth'];

        $this->assertCount(2, $authGroupRoutes[0]['middlewares']);

        $this->assertEquals('auth', $authGroupRoutes[0]['middlewares'][0]);

        $this->assertEquals('owner', $authGroupRoutes[0]['middlewares'][1]);

        $this->assertCount(4, $authGroupRoutes[1]['middlewares']);

        $this->assertEquals('auth', $authGroupRoutes[1]['middlewares'][0]);

        $this->assertEquals('owner', $authGroupRoutes[1]['middlewares'][1]);

        $this->assertEquals('csrf', $authGroupRoutes[1]['middlewares'][2]);

        $this->assertEquals('ddos', $authGroupRoutes[1]['middlewares'][3]);
    }

    public function testRoutesWithNames()
    {
        $this->route->post('post/1', function () {
            info('Getting the first post');
        })->name('first');

        $this->assertIsArray($this->route->getRuntimeRoutes());

        $this->assertEquals('first', $this->route->getRuntimeRoutes()[0]['name']);
    }

    public function testNamingRouteBeforeDefination()
    {
        $this->expectException(RouteException::class);

        $this->expectExceptionMessage('exception.name_before_route_definition');

        $this->route->name('myposts')->get('my-posts', 'PostController', 'myPosts');
    }

    public function testDuplicateNamesOnRoutes()
    {
        $this->expectException(RouteException::class);

        $this->expectExceptionMessage('exception.name_is_not_unique');

        $this->route->post('post/1', 'PostController', 'getPost')->name('post');

        $this->route->post('post/2', 'PostController', 'getPost')->name('post');
    }

    public function testNameOnGroup()
    {
        $this->expectException(RouteException::class);

        $this->expectExceptionMessage('exception.name_on_group');

        $this->route->group('auth', function ($route) {
            $route->add('dashboard', 'GET', 'AuthController', 'dashboard');
        })->name('authGroup');
    }

    public function testNamedRoutesWithGroupRoutes()
    {
        $route = $this->route;

        $this->route->group('auth', function ($route) {
            $route->add('dashboard', 'GET', 'AuthController', 'dashboard');
        });

        $route->add('landing', 'GET', 'MainController', 'landing')->name('landing');

        $this->assertArrayHasKey('name', $this->route->getRuntimeRoutes()[0]);

        $this->assertArrayNotHasKey('group', $this->route->getRuntimeRoutes()[0]);

        $this->assertArrayNotHasKey('name', $this->route->getRuntimeRoutes()[1]);

        $this->assertArrayHasKey('group', $this->route->getRuntimeRoutes()[1]);
    }

    public function testNamedRoutesInGroup()
    {
        $this->route->group('auth', function ($route) {
            $route->add('reports', 'GET', 'MainController', 'landing')->name('reports');
            $route->add('dashboard', 'GET', 'MainController', 'dashboard')->name('dash');
        });

        $this->assertEquals('auth', $this->route->getRuntimeRoutes()[0]['group']);

        $this->assertEquals('reports', $this->route->getRuntimeRoutes()[0]['name']);

        $this->assertEquals('auth', $this->route->getRuntimeRoutes()[1]['group']);

        $this->assertEquals('dash', $this->route->getRuntimeRoutes()[1]['name']);
    }

}
