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
 * Class AssetException
 * @package Quantum\Exceptions
 */
class AssetException extends \Exception
{
    /**
     * @param string $position
     * @param string $name
     * @return \Quantum\Exceptions\AssetException
     */
    public static function positionInUse(string $position, string $name): AssetException
    {
        return new self(t('position_in_use', [$position, $name]), E_WARNING);
    }
}
