<?php

namespace Quantum\Tests\Unit\Router\Helpers;

use Quantum\Router\RouteCollection;
use Quantum\Tests\Unit\AppTestCase;
use Quantum\Router\MatchedRoute;
use Quantum\Router\Route;
use Quantum\Di\Di;

class RouteHelpersTest extends AppTestCase
{

    public function tearDown(): void
    {
        request()->setMatchedRoute(null);
        request()->flush();

        parent::tearDown();
    }

    public function testHelpersReturnDefaultsWhenNoRouteMatched(): void
    {
        $this->assertNull(current_middlewares());
        $this->assertNull(current_module());
        $this->assertNull(current_controller());
        $this->assertNull(current_action());
        $this->assertNull(route_callback());
        $this->assertNull(current_route());
        $this->assertNull(route_pattern());
        $this->assertSame([], route_params());
        $this->assertNull(route_param('id'));
        $this->assertNull(route_cache_settings());
        $this->assertNull(route_name());
        $this->assertNull(route_prefix());
    }

    public function testHelpersReturnValuesForMatchedControllerRoute(): void
    {
        $route = new Route(
            ['GET'],
            '[:alpha:2]?/post/[uuid=:any]',
            'PostController',
            'show'
        );

        $route
            ->module('Web')
            ->prefix('api')
            ->group('content')
            ->name('post.show')
            ->addMiddlewares(['Auth', 'Editor'])
            ->cache(true, 120)
            ->setCompiledPattern('compiled-pattern');

        $matched = new MatchedRoute(
            $route,
            ['uuid' => 'abc-123']
        );

        request()->setMatchedRoute($matched);

        $this->assertSame(['Auth', 'Editor'], current_middlewares());
        $this->assertSame('Web', current_module());
        $this->assertSame('PostController', current_controller());
        $this->assertSame('show', current_action());
        $this->assertSame('[:alpha:2]?/post/[uuid=:any]', current_route());
        $this->assertSame('compiled-pattern', route_pattern());
        $this->assertSame(['uuid' => 'abc-123'], route_params());
        $this->assertSame('abc-123', route_param('uuid'));
        $this->assertSame(['enabled' => true, 'ttl' => 120], route_cache_settings());
        $this->assertSame('post.show', route_name());
        $this->assertSame('api', route_prefix());
    }

    public function testRouteCallbackReturnsClosureForClosureRoute(): void
    {
        $closure = function (): void {
        };

        $route = new Route(
            ['GET'],
            'home',
            null,
            null,
            $closure
        );

        $matched = new MatchedRoute($route, []);

        request()->setMatchedRoute($matched);

        $this->assertSame($closure, route_callback());
        $this->assertNull(current_controller());
        $this->assertNull(current_action());
    }

    public function testFindRouteByNameReturnsRouteFromCollection(): void
    {
        $route = new Route(
            ['GET'],
            'dashboard',
            'DashboardController',
            'index'
        );

        $route->name('Dashboard')->module('Admin');

        $collection = $this->getRouteCollection();
        $collection->add($route);

        $found = find_route_by_name('dashboard', 'admin');

        $this->assertInstanceOf(Route::class, $found);
        $this->assertSame('DashboardController', $found->getController());
    }

    public function testRouteGroupExistsDetectsGroupInModule(): void
    {
        $route = new Route(
            ['GET'],
            'profile',
            'ProfileController',
            'show'
        );

        $route->group('auth')->module('Web');

        $collection = $this->getRouteCollection();
        $collection->add($route);

        $this->assertTrue(route_group_exists('auth', 'web'));
        $this->assertFalse(route_group_exists('guest', 'web'));
        $this->assertFalse(route_group_exists('auth', 'api'));
    }

    public function testRouteMethodAndUri(): void
    {
        request()->create('POST', 'http://example.com/api/test');

        $this->assertSame('POST', route_method());

        $this->assertSame('api/test', route_uri());
    }

    public function testFindRouteByNameDependsOnCollectionState(): void
    {
        $this->assertNull(
            find_route_by_name('route-that-does-not-exist', 'admin'),
            'Expected null when route does not exist in collection'
        );

        $collection = $this->getRouteCollection();

        $this->assertNull(
            find_route_by_name('route-that-does-not-exist', 'admin'),
            'Expected null when route does not exist in current collection'
        );

        $route = new Route(
            ['GET'],
            'dashboard',
            'DashboardController',
            'index'
        );

        $route->name('dashboard')->module('admin');
        $collection->add($route);

        $found = find_route_by_name('dashboard', 'admin');

        $this->assertInstanceOf(Route::class, $found);

        $this->assertSame('DashboardController', $found->getController());
    }

    public function testRouteGroupExistsDependsOnCollectionState(): void
    {
        $this->assertFalse(
            route_group_exists('group-that-does-not-exist', 'web'),
            'Expected false when group does not exist in collection'
        );

        $collection = $this->getRouteCollection();

        $this->assertFalse(
            route_group_exists('group-that-does-not-exist', 'web'),
            'Expected false when group does not exist in current collection'
        );

        $route = new Route(
            ['GET'],
            'profile',
            'ProfileController',
            'show'
        );

        $route->group('auth')->module('web');
        $collection->add($route);

        $this->assertTrue(
            route_group_exists('auth', 'web'),
            'Expected true when matching group exists in module'
        );
    }

    private function getRouteCollection(): RouteCollection
    {
        if (Di::has(RouteCollection::class)) {
            return Di::get(RouteCollection::class);
        }

        $collection = new RouteCollection();
        Di::set(RouteCollection::class, $collection);

        return $collection;
    }

}
