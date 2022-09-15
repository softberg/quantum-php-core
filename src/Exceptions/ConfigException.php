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
 * @since 2.8.0
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
     * @return \Quantum\Exceptions\ConfigException
     */
    public static function configAlreadyLoaded(): ConfigException
    {
        return new static(t('exception.config_already_loaded'), E_WARNING);
    }

    /**
     * @param string $name
     * @return \Quantum\Exceptions\ConfigException
     */
    public static function configCollision(string $name): ConfigException
    {
        return new static(t('exception.config_collision', $name), E_WARNING);
    }

}
