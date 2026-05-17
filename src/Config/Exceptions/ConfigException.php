<?php

declare(strict_types=1);

/**
 * Quantum PHP Framework
 *
 * An open source software development framework for PHP
 *
 * @package Quantum
 * @author Arman Ag. <arman@quantumphp.io>
 * @copyright Copyright (c) 2018 Softberg LLC (https://softberg.org)
 * @link https://quantumphp.io/
 * @since 3.0.0
 */

namespace Quantum\Config\Exceptions;

use Quantum\Config\Enums\ExceptionMessages;
use Quantum\App\Exceptions\BaseException;

/**
 * Class ConfigException
 * @package Quantum\Config
 */
class ConfigException extends BaseException
{
    public static function configCollision(string $name): self
    {
        return new self(
            _message(ExceptionMessages::CONFIG_COLLISION, [$name]),
            E_WARNING
        );
    }
}
