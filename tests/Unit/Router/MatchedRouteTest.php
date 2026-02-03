<?php

namespace Quantum\Tests\Unit\Router;

use Quantum\Tests\Unit\AppTestCase;
use Quantum\Router\MatchedRoute;
use Quantum\Router\Route;

class MatchedRouteTest extends AppTestCase
{
    public function testMatchedRouteStoresRouteAndParams()
    {
        $route = new Route(['GET'], 'users/[id=:num]', 'Ctrl', 'act');
        $params = ['id' => '42'];

        $matched = new MatchedRoute($route, $params);

        $this->assertSame($route, $matched->getRoute());
        $this->assertSame($params, $matched->getParams());
    }

    public function testMatchedRouteSupportsEmptyParams()
    {
        $route = new Route(['GET'], 'users', 'Ctrl', 'act');

        $matched = new MatchedRoute($route, []);

        $this->assertSame([], $matched->getParams());
    }

    public function testMatchedRouteParamsAreReturnedAsGiven()
    {
        $route = new Route(['GET'], 'x', 'Ctrl', 'act');

        $params = [
            'id' => '7',
            'slug' => 'hello',
        ];

        $matched = new MatchedRoute($route, $params);

        $this->assertArrayHasKey('id', $matched->getParams());

        $this->assertArrayHasKey('slug', $matched->getParams());

        $this->assertSame('7', $matched->getParams()['id']);

        $this->assertSame('hello', $matched->getParams()['slug']);
    }

    public function testMatchedRouteRouteInstanceIsExactSameObject()
    {
        $route = new Route(['POST'], 'submit', 'Ctrl', 'act');

        $matched = new MatchedRoute($route, []);

        $this->assertTrue($matched->getRoute() === $route);
    }
}
