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

namespace Quantum\Module\Exceptions;

/**
 * Class ModuleLoaderException
 * @package Quantum\Module
 */
class ModuleException extends \Exception
{
    /**
     * @param string $name
     * @return ModuleException
     */
    public static function moduleRoutesNotFound(string $name): ModuleException
    {
        return new static(t('exception.module_routes_not_found', $name), E_ERROR);
    }

    /**
     * @return ModuleException
     */
    public static function moduleConfigNotFound(): ModuleException
    {
        return new static(t('exception.module_config_not_found'), E_ERROR);
    }

    /**
     * @return ModuleException
     */
    public static function moduleCreationIncomplete(): ModuleException
    {
        return new static("Module creation incomplete: missing files.", E_ERROR);
    }

    /**
     * @param string $name
     * @return ModuleException
     */
    public static function missingModuleTemplate(string $name): ModuleException
    {
        return new static("Template `" . $name . "` does not exist", E_ERROR);
    }

    /**
     * @return ModuleException
     */
    public static function missingModuleDirectory(): ModuleException
    {
        return new static("Module directory does not exist, skipping config update.", E_ERROR);
    }

    /**
     * @param string $name
     * @return ModuleException
     */
    public static function moduleAlreadyExists(string $name): ModuleException
    {
        return new static("A module or prefix named `" . $name . "` already exists", E_ERROR);
    }
}