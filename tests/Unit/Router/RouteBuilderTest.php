<?php

namespace Quantum\Tests\Unit\Router;

use Quantum\Tests\Unit\AppTestCase;
use Quantum\Router\RouteBuilder;
use Quantum\Router\Route;

class RouteBuilderTest extends AppTestCase
{
    public function testRouteBuilderBuildReturnsFlattenedRoutesFromModuleClosures(): void
    {
        $builder = new RouteBuilder();

        $closures = [
            'Api' => function (Route $route): void {
                $route->get('users', 'UsersController', 'index');
                $route->post('login', 'AuthController', 'login');
            },
            'Web' => function (Route $route): void {
                $route->get('', 'HomeController', 'index');
            },
        ];

        $configs = [
            'Api' => ['prefix' => 'api', 'enabled' => true],
            'Web' => ['prefix' => '', 'enabled' => true],
        ];

        $routes = $builder->build($closures, $configs);

        $this->assertIsArray($routes);
        $this->assertCount(3, $routes);

        $this->assertSame('GET', $routes[0]['method']);
        $this->assertSame('api/users', $routes[0]['route']);
        $this->assertSame('Api', $routes[0]['module']);

        $this->assertSame('POST', $routes[1]['method']);
        $this->assertSame('api/login', $routes[1]['route']);
        $this->assertSame('Api', $routes[1]['module']);

        $this->assertSame('GET', $routes[2]['method']);
        $this->assertSame('', $routes[2]['route']);
        $this->assertSame('Web', $routes[2]['module']);
    }

    public function testRouteBuilderBuildUsesEmptyOptionsWhenModuleConfigNotProvided(): void
    {
        $builder = new RouteBuilder();

        $closures = [
            'Test' => function (Route $route): void {
                $route->get('ping', 'PingController', 'index');
            },
        ];

        $routes = $builder->build($closures, []);

        $this->assertCount(1, $routes);
        $this->assertSame('GET', $routes[0]['method']);
        $this->assertSame('ping', $routes[0]['route']);
        $this->assertSame('Test', $routes[0]['module']);
    }
}
