<?php

namespace Quantum\Tests\Unit\Router;

use Quantum\Router\Exceptions\RouteException;
use Quantum\Tests\Unit\AppTestCase;
use Quantum\Router\Route;

class RouteTest extends AppTestCase
{
    public function testRouteControllerRouteConstruction()
    {
        $route = new Route(
            ['get', 'post'],
            'users',
            'UserController',
            'listAction'
        );

        $this->assertSame(['GET', 'POST'], $route->getMethods());

        $this->assertSame('users', $route->getPattern());

        $this->assertSame('UserController', $route->getController());

        $this->assertSame('listAction', $route->getAction());

        $this->assertFalse($route->isClosure());
    }

    public function testRouteClosureRouteConstruction()
    {
        $handler = function () {
        };

        $route = new Route(
            ['GET'],
            'health',
            null,
            null,
            $handler
        );

        $this->assertTrue($route->isClosure());

        $this->assertSame($handler, $route->getClosure());

        $this->assertNull($route->getController());

        $this->assertNull($route->getAction());
    }

    public function testRouteConstructorRejectsEmptyMethods()
    {
        $this->expectException(RouteException::class);

        new Route([], 'users', 'UserController', 'listAction');
    }

    public function testRouteClosureRouteCannotDefineControllerOrAction()
    {
        $this->expectException(RouteException::class);

        new Route(
            ['GET'],
            'health',
            'HealthController',
            'checkAction',
            function () {
            }
        );
    }

    public function testRouteControllerRouteRequiresControllerAndAction()
    {
        $this->expectException(RouteException::class);

        new Route(['GET'], 'users', null, null);
    }

    public function testRouteAllowsMethodIsCaseInsensitive()
    {
        $route = new Route(
            ['GET'],
            'users',
            'UserController',
            'listAction'
        );

        $this->assertTrue($route->allowsMethod('get'));

        $this->assertTrue($route->allowsMethod('GET'));

        $this->assertFalse($route->allowsMethod('POST'));
    }

    public function testRouteCacheConfigurationIsStored()
    {
        $route = new Route(
            ['GET'],
            'reports',
            'ReportController',
            'indexAction'
        );

        $route->cache(true, 120);

        $this->assertSame(
            ['enabled' => true, 'ttl' => 120],
            $route->getCache()
        );
    }

    public function testRouteCompiledPatternCanBeStored()
    {
        $route = new Route(
            ['GET'],
            'users',
            'UserController',
            'listAction'
        );

        $route->setCompiledPattern('^users$');

        $this->assertSame('^users$', $route->getCompiledPattern());
    }

    public function testRouteMiddlewareStackingOrder()
    {
        $route = new Route(
            ['GET'],
            'users',
            'UserController',
            'listAction'
        );

        $route->addMiddlewares(['auth', 'log']);
        $route->addMiddlewares(['csrf', 'throttle']);

        $this->assertSame(
            ['csrf', 'throttle', 'auth', 'log'],
            $route->getMiddlewares()
        );
    }

    public function testRouteToArrayExportsRouteState()
    {
        $route = new Route(
            ['GET'],
            'users',
            'UserController',
            'listAction'
        );

        $route->cache(true, 60);

        $data = $route->toArray();

        $this->assertIsArray($data);

        $this->assertSame(['GET'], $data['methods']);

        $this->assertSame('users', $data['route']);

        $this->assertSame('UserController', $data['controller']);

        $this->assertSame('listAction', $data['action']);

        $this->assertSame(['enabled' => true, 'ttl' => 60], $data['cache']);
    }
}
