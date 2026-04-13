<?php

namespace Quantum\Tests\Unit\Http\Traits\Request;

use Quantum\Tests\Unit\AppTestCase;
use Quantum\Router\MatchedRoute;
use Quantum\Router\Route;

class HttpRequestRouteTest extends AppTestCase
{
    public function tearDown(): void
    {
        request()->setMatchedRoute(null);
        request()->flush();

        parent::tearDown();
    }

    public function testGetMatchedRouteReturnsNullByDefault(): void
    {
        $this->assertNull(request()->getMatchedRoute());
    }

    public function testSetAndGetMatchedRoute(): void
    {
        $route = new Route(
            ['GET'],
            '/test',
            'TestController',
            'tests'
        );

        $matched = new MatchedRoute($route, ['id' => 1]);

        request()->setMatchedRoute($matched);

        $this->assertSame($matched, request()->getMatchedRoute());
    }

    public function testSetMatchedRouteToNullResetsState(): void
    {
        $route = new Route(
            ['GET'],
            '/test',
            'TestController',
            'tests'
        );

        $matched = new MatchedRoute($route, []);

        request()->setMatchedRoute($matched);
        $this->assertNotNull(request()->getMatchedRoute());

        request()->setMatchedRoute(null);
        $this->assertNull(request()->getMatchedRoute());
    }
}
