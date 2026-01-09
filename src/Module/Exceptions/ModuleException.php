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

namespace Quantum\Module\Exceptions;

use Quantum\Module\Enums\ExceptionMessages;

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
    public static function moduleRoutesNotFound(string $name): self
    {
        return new self(
            _message(ExceptionMessages::MODULE_ROUTES_NOT_FOUND, $name),
            E_ERROR
        );
    }

    /**
     * @return ModuleException
     */
    public static function moduleConfigNotFound(): self
    {
        return new self(
            ExceptionMessages::MODULE_CONFIG_NOT_FOUND,
            E_ERROR
        );
    }

    /**
     * @return ModuleException
     */
    public static function moduleCreationIncomplete(): self
    {
        return new self(
            ExceptionMessages::MODULE_CREATION_INCOMPLETE,
            E_ERROR
        );
    }

    /**
     * @param string $name
     * @return ModuleException
     */
    public static function missingModuleTemplate(string $name): self
    {
        return new self(
            _message(ExceptionMessages::MISSING_MODULE_TEMPLATE, [$name]),
            E_ERROR
        );
    }

    /**
     * @return ModuleException
     */
    public static function missingModuleDirectory(): self
    {
        return new self(
            ExceptionMessages::MISSING_MODULE_DIRECTORY,
            E_ERROR
        );
    }

    /**
     * @param string $name
     * @return ModuleException
     */
    public static function moduleAlreadyExists(string $name): self
    {
        return new self(
            _message(ExceptionMessages::MODULE_ALREADY_EXISTS, [$name]),
            E_ERROR
        );
    }
}
