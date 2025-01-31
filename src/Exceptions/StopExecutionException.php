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
 * @since 2.9.5
 */

namespace Quantum\Exceptions;

/**
 * Class StopExecutionException
 * @package Quantum\Exceptions
 */
class StopExecutionException extends BaseException
{
    /**
     * @param int|null $code
     * @return StopExecutionException
     */
    public static function executionTerminated(?int $code): StopExecutionException
    {
        return new static(t('exception.execution_terminated'), $code);
    }
}