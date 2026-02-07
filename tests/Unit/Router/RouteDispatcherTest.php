<?php

namespace Quantum\Tests\Unit\Router;

use Quantum\Libraries\Csrf\Exceptions\CsrfException;
use Quantum\Router\Exceptions\RouteException;
use Quantum\Tests\Unit\AppTestCase;
use Quantum\Router\RouteDispatcher;
use Quantum\Libraries\Csrf\Csrf;
use Quantum\Router\MatchedRoute;
use Quantum\Http\Response;
use Quantum\Router\Route;
use Quantum\Http\Request;
use Mockery;

class RouteDispatcherTest extends AppTestCase
{
    public function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testDispatchExecutesClosureRoute()
    {
        $called = false;
        $receivedParam = null;

        $closure = function (string $id) use (&$called, &$receivedParam) {
            $called = true;
            $receivedParam = $id;
        };

        $route = new Route(['GET'], '/test', null, null, $closure);

        $matched = new MatchedRoute(
            $route,
            ['id' => '123']
        );

        $dispatcher = new RouteDispatcher();

        $request = Mockery::mock(Request::class);
        $response = Mockery::mock(Response::class);

        $dispatcher->dispatch($matched, $request, $response);

        $this->assertTrue($called);
        $this->assertSame('123', $receivedParam);
    }

    public function testDispatchThrowsWhenClosureRouteHasNoClosure()
    {
        $this->expectException(RouteException::class);

        $route = new Route(['GET'], '/broken', null, null, null);

        $matched = new MatchedRoute($route, []);

        $dispatcher = new RouteDispatcher();

        $request = Mockery::mock(Request::class);
        $response = Mockery::mock(Response::class);

        $dispatcher->dispatch($matched, $request, $response);
    }

    public function testDispatchExecutesControllerActionWithParams()
    {
        $controllerClass = new class () {
            public static ?string $received = null;

            public function post(string $uuid): void
            {
                self::$received = $uuid;
            }
        };

        $route = new Route(['GET'], '[:alpha:2]?/post/[uuid=:any]', get_class($controllerClass), 'post', null);

        $matched = new MatchedRoute($route, ['uuid' => 'abc-123']);

        $dispatcher = new RouteDispatcher();

        $request = Mockery::mock(Request::class);
        $response = Mockery::mock(Response::class);

        $dispatcher->dispatch($matched, $request, $response);

        $this->assertSame(
            'abc-123',
            $controllerClass::$received,
            'Controller action did not receive matched parameters'
        );
    }

    public function testDispatchCallsControllerHooksInCorrectOrder()
    {
        $controllerClass = new class () {
            public static array $calls = [];

            public function __before(string $uuid): void
            {
                self::$calls[] = 'before:' . $uuid;
            }

            public function post(string $uuid): void
            {
                self::$calls[] = 'action:' . $uuid;
            }

            public function __after(string $uuid): void
            {
                self::$calls[] = 'after:' . $uuid;
            }
        };

        $route = new Route(['GET', 'POST'], '[:alpha:2]?/post/[uuid=:any]', get_class($controllerClass), 'post', null);

        $matched = new MatchedRoute($route, ['uuid' => 'abc']);

        $dispatcher = new RouteDispatcher();

        $request = Mockery::mock(Request::class);
        $request->shouldReceive('getMethod')->andReturn('POST');

        $response = Mockery::mock(Response::class);

        $dispatcher->dispatch($matched, $request, $response);

        $this->assertSame(
            [
                'before:abc',
                'action:abc',
                'after:abc',
            ],
            $controllerClass::$calls,
            'Controller hooks were not executed in the correct order'
        );
    }

    public function testDispatchThrowsWhenControllerActionDoesNotExist()
    {
        $this->expectException(RouteException::class);

        $controllerClass = new class () {
            // Intentionally no action method
        };

        $route = new Route(['GET'], '[:alpha:2]?/broken', get_class($controllerClass), 'missingAction', null);

        $matched = new MatchedRoute($route, []);

        $dispatcher = new RouteDispatcher();

        $request = Mockery::mock(Request::class);
        $response = Mockery::mock(Response::class);

        $dispatcher->dispatch($matched, $request, $response);
    }

    public function testDispatchFailsWhenCsrfIsEnabledAndTokenIsMissing()
    {
        $this->expectException(CsrfException::class);

        $controllerClass = new class () {
            public bool $csrfVerification = true;

            public function submit(): void
            {
                // noop
            }
        };

        $route = new Route(['POST'], '[:alpha:2]?/submit', get_class($controllerClass), 'submit', null);

        $matched = new MatchedRoute($route, []);

        $request = Mockery::mock(Request::class);
        $request->shouldReceive('getMethod')->andReturn('POST');
        $request->shouldReceive('has')
            ->with(Csrf::TOKEN_KEY)
            ->andReturn(false);

        $response = Mockery::mock(Response::class);

        $dispatcher = new RouteDispatcher();

        $dispatcher->dispatch($matched, $request, $response);
    }
}
