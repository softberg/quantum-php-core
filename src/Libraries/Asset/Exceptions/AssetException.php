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
 * @since 2.9.9
 */

namespace Quantum\Libraries\Asset\Exceptions;

use Quantum\Libraries\Asset\Enums\ExceptionMessages;

/**
 * Class AssetException
 * @package Quantum\Libraries\Asset
 */
class AssetException extends \Exception
{
    /**
     * @param string $position
     * @param string $name
     * @return AssetException
     */
    public static function positionInUse(string $position, string $name): AssetException
    {
        return new self(_message(ExceptionMessages::POSITION_IN_USE, [$position, $name]), E_WARNING);
    }

    /**
     * @param string|null $name
     * @return AssetException
     */
    public static function nameInUse(?string $name): AssetException
    {
        return new self(_message(ExceptionMessages::NAME_IN_USE, [$name]), E_WARNING);
    }
}