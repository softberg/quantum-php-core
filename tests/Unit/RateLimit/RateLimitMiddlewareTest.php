<?php

namespace Quantum\Tests\Unit\RateLimit;

use Quantum\RateLimit\Contracts\RateLimitAdapterInterface;
use Quantum\RateLimit\Factories\RateLimiterFactory;
use Quantum\Tests\Unit\AppTestCase;
use Quantum\RateLimit\RateLimitMiddleware;
use Quantum\Router\Route;
use Quantum\Http\Request;
use Quantum\Http\Response;
use Quantum\RateLimit\RateLimiter;
use Quantum\Di\Di;

class RateLimitMiddlewareTest extends AppTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        if (!Di::isRegistered(RateLimiterFactory::class)) {
            Di::register(RateLimiterFactory::class);
        }

        $factory = Di::get(RateLimiterFactory::class);
        $this->setPrivateProperty($factory, 'instances', [
            'file' => new RateLimiter(new ToggleAdapter()),
        ]);
    }

    public function testRateLimitMiddlewareBlocksWith429AndHeaders(): void
    {
        $route = new Route(['GET'], '/posts', 'PostController', 'index');
        $route->rateLimit(1, 60);

        $middleware = new RateLimitMiddleware($route);

        $first = $middleware->apply(
            request(),
            fn (Request $request): Response => response()->json(['status' => 'ok'])
        );

        $this->assertSame(200, $first->getStatusCode());
        $this->assertSame('1', $first->getHeader('X-RateLimit-Limit'));

        response()->flush();

        $second = $middleware->apply(
            request(),
            fn (Request $request): Response => response()->json(['status' => 'ok'])
        );

        $this->assertSame(429, $second->getStatusCode());
        $this->assertSame('1', $second->getHeader('X-RateLimit-Limit'));
        $this->assertSame('0', $second->getHeader('X-RateLimit-Remaining'));
        $this->assertSame('7', $second->getHeader('Retry-After'));
        $this->assertSame('{"message":"Too Many Requests"}', $second->getContent());
    }
}

class ToggleAdapter implements RateLimitAdapterInterface
{
    private int $calls = 0;

    public function hit(string $key, int $limit, int $interval): bool
    {
        $this->calls++;
        return $this->calls === 1;
    }

    public function reset(string $key, int $count = 0): void
    {
    }

    public function retryAfter(string $key): int
    {
        return 7;
    }
}
