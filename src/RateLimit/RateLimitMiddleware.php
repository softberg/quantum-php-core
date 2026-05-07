<?php

declare(strict_types=1);

/**
 * Quantum PHP Framework
 *
 * An open source software development framework for PHP
 *
 * @package Quantum
 * @author Arman Ag. <arman.ag@softberg.org>
 * @copyright Copyright (c) 2018 Softberg LLC (https://softberg.org)
 * @link http://quantum.softberg.org/
 * @since 3.0.0
 */

namespace Quantum\RateLimit;

use Quantum\RateLimit\Factories\RateLimiterFactory;
use Quantum\Http\Enums\StatusCode;
use Quantum\Middleware\Middleware;
use Quantum\Router\Route;
use Quantum\Http\Response;
use Quantum\Http\Request;
use Closure;

class RateLimitMiddleware extends Middleware
{
    private Route $route;

    public function __construct(Route $route)
    {
        $this->route = $route;
    }

    public function apply(Request $request, Closure $next): Response
    {
        $settings = $this->route->getRateLimit();

        if ($settings === null) {
            return $next($request);
        }

        $limit = (int) ($settings['limit'] ?? 0);
        $interval = (int) ($settings['interval'] ?? 0);

        if ($limit <= 0 || $interval <= 0) {
            return $next($request);
        }

        $limiter = RateLimiterFactory::get();
        $method = $request->getMethod() ?? 'GET';
        $pattern = $this->route->getPattern();
        $ip = get_user_ip();

        $allowed = $limiter->hit($method, $pattern, $ip, $limit, $interval);

        if (!$allowed) {
            return response()
                ->setHeader('X-RateLimit-Limit', (string) $limit)
                ->setHeader('X-RateLimit-Remaining', '0')
                ->setHeader('Retry-After', (string) $interval)
                ->json(['message' => 'Too Many Requests'], StatusCode::TOO_MANY_REQUESTS);
        }

        return $next($request)
            ->setHeader('X-RateLimit-Limit', (string) $limit);
    }
}
