<?php

namespace Quantum\Tests\Unit\Router;

use Quantum\Tests\Unit\AppTestCase;
use Quantum\Router\RouteCollection;
use Quantum\Router\MatchedRoute;
use Quantum\Router\RouteFinder;
use Quantum\Router\Route;

class RouteFinderTest extends AppTestCase
{
    private RouteCollection $collection;
    private RouteFinder $finder;

    public function setUp(): void
    {
        parent::setUp();

        $this->collection = new RouteCollection();
        $this->finder = new RouteFinder($this->collection);
    }

    public function testRouteFinderFindReturnsMatchedRouteForStaticMatch(): void
    {
        $route = new Route(['GET'], 'users', 'Ctrl', 'act');
        $this->collection->add($route);

        $req = request();
        $req->create('GET', '/users');

        $result = $this->finder->find($req);

        $this->assertInstanceOf(MatchedRoute::class, $result);
        $this->assertSame($route, $result->getRoute());
        $this->assertSame([], $result->getParams());
    }

    public function testRouteFinderFindReturnsNullWhenNoMatch(): void
    {
        $route = new Route(['GET'], 'users', 'Ctrl', 'act');
        $this->collection->add($route);

        $req = request();
        $req->create('GET', '/posts');

        $this->assertNull($this->finder->find($req));
    }

    public function testRouteFinderFindSkipsWrongHttpMethod(): void
    {
        $route = new Route(['POST'], 'users', 'Ctrl', 'act');
        $this->collection->add($route);

        $req = request();
        $req->create('GET', '/users');

        $this->assertNull($this->finder->find($req));
    }

    public function testRouteFinderFindReturnsFirstMatchingRouteOnly(): void
    {
        $r1 = new Route(['GET'], 'users', 'C1', 'a1');
        $r2 = new Route(['GET'], 'users', 'C2', 'a2');

        $this->collection->add($r1);
        $this->collection->add($r2);

        $req = request();
        $req->create('GET', '/users');

        $result = $this->finder->find($req);

        $this->assertSame($r1, $result->getRoute());
    }

    public function testRouteFinderFindPassesExtractedParams(): void
    {
        $route = new Route(['GET'], 'users/[id=:num]', 'Ctrl', 'act');
        $this->collection->add($route);

        $req = request();
        $req->create('GET', '/users/42');

        $result = $this->finder->find($req);

        $this->assertSame(['id' => '42'], $result->getParams());
    }

    public function testRouteFinderFindWithMultipleRoutesOnlyOneMatches(): void
    {
        $r1 = new Route(['GET'], 'posts', 'Ctrl', 'act');
        $this->collection->add($r1);

        $r2 = new Route(['GET'], 'users', 'C', 'a');
        $this->collection->add($r2);

        $req = request();
        $req->create('GET', '/users');

        $result = $this->finder->find($req);

        $this->assertSame($r2, $result->getRoute());
    }
}
