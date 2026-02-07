<?php

namespace Quantum\Tests\Unit\Router\Helpers;

use Quantum\Router\RouteCollection;
use Quantum\Tests\Unit\AppTestCase;
use Quantum\Router\MatchedRoute;
use Quantum\Router\Route;
use Quantum\Http\Request;
use Quantum\Di\Di;

class RouteHelpersTest extends AppTestCase
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

    public function testHelpersReturnDefaultsWhenNoRouteMatched()
    {
        $this->assertNull(current_middlewares());
        $this->assertNull(current_module());
        $this->assertNull(current_controller());
        $this->assertNull(current_action());
        $this->assertNull(route_callback());
        $this->assertNull(current_route());
        $this->assertSame('', route_pattern());
        $this->assertSame([], route_params());
        $this->assertNull(route_param('id'));
        $this->assertNull(route_cache_settings());
        $this->assertNull(route_name());
        $this->assertNull(route_prefix());
    }

    public function testHelpersReturnValuesForMatchedControllerRoute()
    {
        $route = new Route(
            ['GET'],
            '[:alpha:2]?/post/[uuid=:any]',
            'PostController',
            'show',
            null
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

        Request::setMatchedRoute($matched);

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

    public function testRouteCallbackReturnsClosureForClosureRoute()
    {
        $closure = function () {
        };

        $route = new Route(
            ['GET'],
            'home',
            null,
            null,
            $closure
        );

        $matched = new MatchedRoute($route, []);

        Request::setMatchedRoute($matched);

        $this->assertSame($closure, route_callback());
        $this->assertNull(current_controller());
        $this->assertNull(current_action());
    }

    public function testFindRouteByNameReturnsRouteFromCollection()
    {
        $route = new Route(
            ['GET'],
            'dashboard',
            'DashboardController',
            'index',
            null
        );

        $route->name('Dashboard')->module('Admin');

        $collection = new RouteCollection();
        $collection->add($route);

        Di::set(RouteCollection::class, $collection);

        $found = find_route_by_name('dashboard', 'admin');

        $this->assertInstanceOf(Route::class, $found);
        $this->assertSame('DashboardController', $found->getController());
    }

    public function testRouteGroupExistsDetectsGroupInModule()
    {
        $route = new Route(
            ['GET'],
            'profile',
            'ProfileController',
            'show',
            null
        );

        $route->group('auth')->module('Web');

        $collection = new RouteCollection();
        $collection->add($route);

        Di::set(RouteCollection::class, $collection);

        $this->assertTrue(route_group_exists('auth', 'web'));
        $this->assertFalse(route_group_exists('guest', 'web'));
        $this->assertFalse(route_group_exists('auth', 'api'));
    }

    public function testRouteMethodAndUri()
    {
        $request = new Request();
        $request->create('POST', 'http://example.com/api/test');

        $this->assertSame('POST', route_method());

        $this->assertSame('api/test', route_uri());
    }

    public function testFindRouteByNameDependsOnCollectionRegistration()
    {
        $this->assertNull(
            find_route_by_name('dashboard', 'admin'),
            'Expected null when RouteCollection is not registered'
        );

        $collection = new RouteCollection();
        Di::set(RouteCollection::class, $collection);

        $this->assertNull(
            find_route_by_name('dashboard', 'admin'),
            'Expected null when RouteCollection is empty'
        );

        $route = new Route(
            ['GET'],
            'dashboard',
            'DashboardController',
            'index',
            null
        );

        $route->name('dashboard')->module('admin');
        $collection->add($route);

        $found = find_route_by_name('dashboard', 'admin');

        $this->assertInstanceOf(Route::class, $found);

        $this->assertSame('DashboardController', $found->getController());
    }

    public function testRouteGroupExistsDependsOnCollectionRegistration()
    {
        $this->assertFalse(
            route_group_exists('auth', 'web'),
            'Expected false when RouteCollection is not registered'
        );

        $collection = new RouteCollection();
        Di::set(RouteCollection::class, $collection);

        $this->assertFalse(
            route_group_exists('auth', 'web'),
            'Expected false when RouteCollection is empty'
        );

        $route = new Route(
            ['GET'],
            'profile',
            'ProfileController',
            'show',
            null
        );

        $route->group('auth')->module('web');
        $collection->add($route);

        $this->assertTrue(
            route_group_exists('auth', 'web'),
            'Expected true when matching group exists in module'
        );
    }

}
