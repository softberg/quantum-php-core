<?php

namespace Quantum\Tests\_root\modules\Test\Middlewares {
    use Quantum\Middleware\QtMiddleware;
    use Quantum\Http\Request;
    use Quantum\Http\Response;
    use Closure;

    class TestMwOne extends QtMiddleware
    {
        public static array $calls = [];

        public function apply(Request $request, Response $response, Closure $next): Response
        {
            self::$calls[] = 'TestMwOne';
            return $next($request, $response);
        }
    }

    class TestMwTwo extends QtMiddleware
    {
        public function apply(Request $request, Response $response, Closure $next): Response
        {
            TestMwOne::$calls[] = 'TestMwTwo';
            $response->setHeader('X-Test', 'Passed');
            return $next($request, $response);
        }
    }

    class TestMwBlocker extends QtMiddleware
    {
        public function apply(Request $request, Response $response, Closure $next): Response
        {
            TestMwOne::$calls[] = 'TestMwBlocker';
            return $response->json(['status' => 'blocked']);
        }
    }

    class InvalidMw
    {
        public function apply(Request $request, Response $response, Closure $next)
        {
            return $next($request, $response);
        }
    }
}

namespace Quantum\Tests\Unit\Middleware {

    use Quantum\Tests\Unit\AppTestCase;
    use Quantum\Middleware\MiddlewareManager;
    use Quantum\Middleware\Exceptions\MiddlewareException;
    use Quantum\Router\MatchedRoute;
    use Quantum\Router\Route;
    use Mockery;
    use Quantum\Tests\_root\modules\Test\Middlewares\TestMwOne;

    class MiddlewareManagerTest extends AppTestCase
    {
        public function tearDown(): void
        {
            Mockery::close();
            parent::tearDown();
        }

        public function testMiddlewareManagerExecutesSequentially(): void
        {
            TestMwOne::$calls = [];

            $route = new Route(['GET'], '/test-route', 'TestController', 'index');
            $route->addMiddlewares(['TestMwOne', 'TestMwTwo']);
            $route->module('Test');

            $matchedRoute = new MatchedRoute($route, []);

            $request = request();
            $response = response();

            $manager = new MiddlewareManager($matchedRoute);
            $result = $manager->applyMiddlewares($request, $response);

            $this->assertSame($response, $result);
            $this->assertEquals(['TestMwOne', 'TestMwTwo'], TestMwOne::$calls);
            $this->assertEquals('Passed', $result->getHeader('X-Test'));
        }

        public function testMiddlewareManagerCanShortCircuit(): void
        {
            TestMwOne::$calls = [];

            $route = new Route(['GET'], '/test-route', 'TestController', 'index');
            $route->addMiddlewares(['TestMwOne', 'TestMwBlocker', 'TestMwTwo']);
            $route->module('Test');

            $matchedRoute = new MatchedRoute($route, []);

            $request = request();
            $response = response();

            $manager = new MiddlewareManager($matchedRoute);
            $result = $manager->applyMiddlewares($request, $response);

            $this->assertSame($response, $result);
            $this->assertEquals(['TestMwOne', 'TestMwBlocker'], TestMwOne::$calls);
            $this->assertEquals('{"status":"blocked"}', $result->getContent());
            $this->assertNull($result->getHeader('X-Test'));
        }

        public function testMiddlewareManagerThrowsWhenMiddlewareDoesNotExist(): void
        {
            $this->expectException(MiddlewareException::class);

            $route = new Route(['GET'], '/test-route', 'TestController', 'index');
            $route->addMiddlewares(['NonExistentMw']);
            $route->module('Test');

            $matchedRoute = new MatchedRoute($route, []);

            $manager = new MiddlewareManager($matchedRoute);
            $manager->applyMiddlewares(request(), response());
        }

        public function testMiddlewareManagerThrowsWhenNotInstanceOfQtMiddleware(): void
        {
            $this->expectException(MiddlewareException::class);

            $route = new Route(['GET'], '/test-route', 'TestController', 'index');
            $route->addMiddlewares(['InvalidMw']);
            $route->module('Test');

            $matchedRoute = new MatchedRoute($route, []);

            $manager = new MiddlewareManager($matchedRoute);
            $manager->applyMiddlewares(request(), response());
        }
    }
}
