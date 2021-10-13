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
 * @since 2.3.0
 */

namespace Quantum\Exceptions;

/**
 * ConfigException class
 *
 * @package Quantum
 * @category Exceptions
 */
class ConfigException extends \Exception
{
    /**
     * Config file not found message
     */
    const CONFIG_FILE_NOT_FOUND = 'Config file `{%1}` does not exists';

    /**
     * Setup not provided to load
     */
    const CONFIG_ALREADY_LOADED = 'Config already loaded, use method `import()` to import additional config data';

    /**
     * Config collision message
     */
    const CONFIG_COLLISION = 'Config key `{%1}` is already in use';

    /**
     * @return \Quantum\Exceptions\ConfigException
     */
    public static function configAlreadyLoaded(): ConfigException
    {
        return new static(self::CONFIG_ALREADY_LOADED, E_WARNING);
    }

    /**
     * @param string $name
     * @return \Quantum\Exceptions\ConfigException
     */
    public static function configCollision(string $name): ConfigException
    {
        return new static(_message(self::CONFIG_COLLISION, $name), E_WARNING);
    }

}
