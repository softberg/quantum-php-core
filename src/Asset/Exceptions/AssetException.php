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

namespace Quantum\Asset\Exceptions;

use Quantum\Asset\Enums\ExceptionMessages;

/**
 * Class AssetException
 * @package Quantum\Asset
 */
class AssetException extends \Exception
{
    public static function positionInUse(int $position, string $name): AssetException
    {
        return new self(
            _message(ExceptionMessages::POSITION_IN_USE, [$position, $name]),
            E_WARNING
        );
    }

    public static function nameInUse(?string $name): AssetException
    {
        return new self(
            _message(ExceptionMessages::NAME_IN_USE, [$name]),
            E_WARNING
        );
    }
}
