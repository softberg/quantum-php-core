<?php

namespace Quantum\Tests\Unit\RateLimit;

use Quantum\RateLimit\Contracts\RateLimitAdapterInterface;
use Quantum\Tests\Unit\AppTestCase;
use Quantum\RateLimit\RateLimiter;

class RateLimiterTest extends AppTestCase
{
    public function testRateLimiterBuildsNormalizedKey(): void
    {
        $adapter = new class () implements RateLimitAdapterInterface {
            public function hit(string $key, int $limit, int $interval): bool
            {
                return true;
            }

            public function reset(string $key, int $count = 0): void
            {
            }

            public function retryAfter(string $key): int
            {
                return 0;
            }
        };

        $limiter = new RateLimiter($adapter);

        $this->assertSame(
            'POST:/api/posts:127.0.0.1',
            $limiter->buildKey('post', ' /api/posts ', '127.0.0.1')
        );
    }

    public function testRateLimiterUsesFallbackKeyPartsWhenEmpty(): void
    {
        $adapter = new class () implements RateLimitAdapterInterface {
            public function hit(string $key, int $limit, int $interval): bool
            {
                return true;
            }

            public function reset(string $key, int $count = 0): void
            {
            }

            public function retryAfter(string $key): int
            {
                return 0;
            }
        };

        $limiter = new RateLimiter($adapter);

        $this->assertSame('GET:/:0.0.0.0', $limiter->buildKey('get', '', null));
    }

    public function testRateLimiterDelegatesHitToAdapter(): void
    {
        $calls = [];

        $adapter = new class ($calls) implements RateLimitAdapterInterface {
            private array $calls;

            public function __construct(array &$calls)
            {
                $this->calls = &$calls;
            }

            public function hit(string $key, int $limit, int $interval): bool
            {
                $this->calls[] = [$key, $limit, $interval];
                return false;
            }

            public function reset(string $key, int $count = 0): void
            {
            }

            public function retryAfter(string $key): int
            {
                return 0;
            }
        };

        $limiter = new RateLimiter($adapter);

        $allowed = $limiter->hit('get', '/status', '10.0.0.1', 100, 60);

        $this->assertFalse($allowed);
        $this->assertSame([['GET:/status:10.0.0.1', 100, 60]], $calls);
    }

    public function testRateLimiterDelegatesResetToAdapter(): void
    {
        $calls = [];

        $adapter = new class ($calls) implements RateLimitAdapterInterface {
            private array $calls;

            public function __construct(array &$calls)
            {
                $this->calls = &$calls;
            }

            public function hit(string $key, int $limit, int $interval): bool
            {
                return true;
            }

            public function reset(string $key, int $count = 0): void
            {
                $this->calls[] = [$key, $count];
            }

            public function retryAfter(string $key): int
            {
                return 0;
            }
        };

        $limiter = new RateLimiter($adapter);

        $limiter->reset('post', '/api/posts', '10.0.0.2', 3);

        $this->assertSame([['POST:/api/posts:10.0.0.2', 3]], $calls);
    }

    public function testRateLimiterDelegatesRetryAfterToAdapter(): void
    {
        $adapter = new class implements RateLimitAdapterInterface {
            public function hit(string $key, int $limit, int $interval): bool
            {
                return true;
            }

            public function reset(string $key, int $count = 0): void
            {
            }

            public function retryAfter(string $key): int
            {
                return $key === 'GET:/status:10.0.0.1' ? 7 : 0;
            }
        };

        $limiter = new RateLimiter($adapter);

        $this->assertSame(7, $limiter->retryAfter('get', '/status', '10.0.0.1'));
    }
}
