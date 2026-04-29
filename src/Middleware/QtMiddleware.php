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

namespace Quantum\Middleware;

use Quantum\Http\Response;
use Quantum\Http\Request;
use Closure;

/**
 * Class QtMiddleware
 * @package Quantum\Middleware
 */
abstract class QtMiddleware
{
    /**
     * Applies the middleware
     */
    abstract public function apply(Request $request, Closure $next): Response;

}
