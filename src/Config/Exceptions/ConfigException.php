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
 * @since 2.9.9
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

    /**
     * @param string $name
     * @return ConfigException
     */
    public static function configCollision(string $name): ConfigException
    {
        return new static(_message(ExceptionMessages::CONFIG_COLLISION, [$name]), E_WARNING);
    }
}