<?php

namespace Quantum\Tests\Unit\Router;

use Quantum\Router\RouteCollection;
use Quantum\Tests\Unit\AppTestCase;
use Quantum\Router\Route;

class RouteCollectionTest extends AppTestCase
{
    public function testRouteCollectionStartsEmpty()
    {
        $collection = new RouteCollection();

        $this->assertSame([], $collection->all());

        $this->assertSame(0, $collection->count());
    }

    public function testRouteCollectionAddIncreasesCount()
    {
        $collection = new RouteCollection();

        $collection->add(new Route(['GET'], 'a', 'Ctrl', 'act'));

        $collection->add(new Route(['POST'], 'b', 'Ctrl', 'act'));

        $this->assertSame(2, $collection->count());
    }

    public function testRouteCollectionAllReturnsAddedRoutes()
    {
        $collection = new RouteCollection();

        $r1 = new Route(['GET'], 'users', 'Ctrl', 'act');

        $r2 = new Route(['GET'], 'posts', 'Ctrl', 'act');

        $collection->add($r1);
        $collection->add($r2);

        $all = $collection->all();

        $this->assertSame([$r1, $r2], $all);
    }

    public function testRouteCollectionInsertionOrderIsPreserved()
    {
        $collection = new RouteCollection();

        $r1 = new Route(['GET'], 'first', 'Ctrl', 'act');

        $r2 = new Route(['GET'], 'second', 'Ctrl', 'act');

        $collection->add($r1);
        $collection->add($r2);

        $all = $collection->all();

        $this->assertSame($r1, $all[0]);
        $this->assertSame($r2, $all[1]);
    }

    public function testRouteCollectionAllReturnsSameInstances()
    {
        $collection = new RouteCollection();

        $r = new Route(['GET'], 'x', 'C', 'a');
        $collection->add($r);

        $this->assertTrue($collection->all()[0] === $r);
    }
}
