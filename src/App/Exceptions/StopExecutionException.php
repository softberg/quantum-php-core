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

namespace Quantum\App\Exceptions;

use Quantum\App\Enums\ExceptionMessages;

/**
 * Class StopExecutionException
 * @package Quantum\App
 */
class StopExecutionException extends BaseException
{
    /**
     * @param int|null $code
     * @return StopExecutionException
     */
    public static function executionTerminated(?int $code): self
    {
        return new self(
            ExceptionMessages::EXECUTION_TERMINATED,
            $code
        );
    }
}
