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

namespace Quantum\App\Traits;

/**
 * Class AppTrait
 * @package Quantum\App
 */
trait AppTrait
{
    /**
     * Sets the app base directory
     */
    public static function setBaseDir(string $baseDir): void
    {
        self::$baseDir = $baseDir;
    }

    /**
     * Gets the app base directory
     */
    public static function getBaseDir(): string
    {
        return self::$baseDir;
    }
}
