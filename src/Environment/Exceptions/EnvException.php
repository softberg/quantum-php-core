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

namespace Quantum\Environment\Exceptions;

use Quantum\Environment\Enums\ExceptionMessages;
use Quantum\App\Exceptions\BaseException;

/**
 * Class EnvException
 * @package Quantum\Exceptions
 */
class EnvException extends BaseException
{

    /**
     * @return EnvException
     */
    public static function environmentImmutable(): self
    {
        return new self(
            ExceptionMessages::IMMUTABLE_ENVIRONMENT,
            E_ERROR
        );
    }

    /**
     * @return EnvException
     */
    public static function environmentNotLoaded(): self
    {
        return new self(
            ExceptionMessages::ENVIRONMENT_NOT_LOADED,
            E_ERROR
        );
    }
}
