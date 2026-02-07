<?php

namespace Http\Traits\Request;

use Quantum\Tests\Unit\AppTestCase;
use Quantum\Router\MatchedRoute;
use Quantum\Http\Request;
use Quantum\Router\Route;
use Quantum\Di\Di;

class HttpRequestRouteTest extends AppTestCase
{
    public function tearDown(): void
    {
        Request::setMatchedRoute(null);
        Request::flush();

        if (Di::isRegistered(Request::class)) {
            $diRequest = Di::get(Request::class);
            $diRequest->setMatchedRoute(null);
            $diRequest->flush();
        }

        parent::tearDown();
    }

    public function testGetMatchedRouteReturnsNullByDefault()
    {
        $this->assertNull(Request::getMatchedRoute());
    }

    public function testSetAndGetMatchedRoute()
    {
        $route = new Route(
            ['GET'],
            '/test',
            'TestController',
            'tests',
            null
        );

        $matched = new MatchedRoute($route, ['id' => 1]);

        Request::setMatchedRoute($matched);

        $this->assertSame($matched, Request::getMatchedRoute());
    }

    public function testSetMatchedRouteToNullResetsState()
    {
        $route = new Route(
            ['GET'],
            '/test',
            'TestController',
            'tests',
            null
        );

        $matched = new MatchedRoute($route, []);

        Request::setMatchedRoute($matched);
        $this->assertNotNull(Request::getMatchedRoute());

        Request::setMatchedRoute(null);
        $this->assertNull(Request::getMatchedRoute());
    }
}
