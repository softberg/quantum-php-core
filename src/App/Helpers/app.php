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
 * @since 2.9.7
 */

use Quantum\App\Exceptions\StopExecutionException;

/**
 * Stops the app execution
 * @param Closure|null $closure
 * @param int|null $code
 * @return mixed
 * @throws StopExecutionException
 */
function stop(Closure $closure = null, ?int $code = 0)
{
    if ($closure) {
        $closure();
    }

    throw StopExecutionException::executionTerminated($code);
}
