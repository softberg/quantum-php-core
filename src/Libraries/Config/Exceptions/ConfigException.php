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

namespace Quantum\Libraries\Config\Exceptions;

use Quantum\Exceptions\BaseException;

/**
 * Class ConfigException
 * @package Quantum\Libraries\Config
 */
class ConfigException extends BaseException
{
    /**
     * @return ConfigException
     */
    public static function configAlreadyLoaded(): ConfigException
    {
        return new static(t('exception.config_already_loaded'), E_WARNING);
    }

    /**
     * @param string $name
     * @return ConfigException
     */
    public static function configCollision(string $name): ConfigException
    {
        return new static('Config key `' . $name . '` is already in use', E_WARNING);
    }
}