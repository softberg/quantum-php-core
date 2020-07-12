<?php

namespace Quantum\Test\Unit {

    use PHPUnit\Framework\TestCase;
    use Quantum\Routes\Route;

    class RouteTest extends TestCase
    {

        private $route;

        public function setUp(): void
        {
            $this->route = new Route('Test');
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

        public function testGroupRoute()
        {
            $route = $this->route;

            $this->route->group('auth', function($route) {
                $route->add('dashboard', 'GET', 'AuthController', 'dashboard');
                $route->add('users', 'GET', 'AuthController', 'users');
            });

            $this->assertCount(2, $this->route->getRuntimeRoutes());

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
            $this->route->group('auth', function($route) {
                $route->add('dashboard', 'GET', 'AuthController', 'dashboard');
                $route->add('user', 'POST', 'AuthController', 'add')->middlewares(['csrf']);
            })->middlewares(['auth']);

            $authGroupRoutes = $this->route->getVirtualRoutes()['auth'];

            $this->assertEquals('auth', $authGroupRoutes[0]['middlewares'][0]);

            $this->assertCount(2, $authGroupRoutes[1]['middlewares']);

            $this->assertEquals('auth', $authGroupRoutes[1]['middlewares'][0]);

            $this->assertEquals('csrf', $authGroupRoutes[1]['middlewares'][1]);
        }

    }

}
    