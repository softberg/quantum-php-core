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

namespace Quantum\Router\Exceptions;

use Quantum\Router\Enums\ExceptionMessages;
use Quantum\App\Exceptions\BaseException;

/**
 * ControllerException class
 * @package Quantum\Router
 */
class RouteControllerException extends BaseException
{
    /**
     * @param string|null $name
     * @return RouteControllerException
     */
    public static function controllerNotDefined(?string $name): self
    {
        return new self(
            _message(ExceptionMessages::CONTROLLER_NOT_FOUND, [$name]),
            E_ERROR
        );
    }

    /**
     * @param string $name
     * @return RouteControllerException
     */
    public static function actionNotDefined(string $name): self
    {
        return new self(
            _message(ExceptionMessages::ACTION_NOT_DEFINED, [$name]),
            E_ERROR
        );
    }
}
