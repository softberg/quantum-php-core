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
 * @since 3.0.0
 */

use Quantum\App\Exceptions\StopExecutionException;

/**
 * Stops the app execution
 * @return never
 * @throws StopExecutionException
 */
function stop(?Closure $closure = null, ?int $code = 0): void
{
    if ($closure instanceof Closure) {
        $closure();
    }

    throw StopExecutionException::executionTerminated($code);
}
