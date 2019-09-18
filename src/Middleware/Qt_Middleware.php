<?php

/**
 * Quantum PHP Framework
 *
 * An open source software development framework for PHP
 *
 * @package Quantum
 * @author Arman Ag. <arman.ag@softberg.org>
 * @copyright Copyright (c) 2018 Softberg LLC (https://softberg.org)
 * @link http://quantum.softberg.org/
 * @since 1.4.0
 */

namespace Quantum\Middleware;

use Quantum\Http\Request;
use Quantum\Http\Response;

/**
 * Class Qt_Middleware
 * @package Quantum\Middleware
 */
abstract class Qt_Middleware
{

    /**
     * Apply
     *
     * Applies the middleware
     *
     * @param Request $request
     * @param \Closure $next
     * @return mixed
     */
    abstract public function apply(Request $request, Response $response, \Closure $next);

}
