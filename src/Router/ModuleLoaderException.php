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

namespace Quantum\Router;

/**
 * Class ModuleLoaderException
 * @package Quantum\Exceptions
 */
class ModuleLoaderException extends \Exception
{
    /**
     * @param string $name
     * @return ModuleLoaderException
     */
    public static function moduleRoutesNotFound(string $name): ModuleLoaderException
    {
        return new static(t('exception.module_routes_not_found', $name), E_ERROR);
    }

    /**
     * @return ModuleLoaderException
     */
    public static function moduleConfigNotFound(): ModuleLoaderException
    {
        return new static(t('exception.module_config_not_found'), E_ERROR);
    }
}
