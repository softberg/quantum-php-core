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

namespace Quantum\Archive\Factories;

use Quantum\Archive\Exceptions\ArchiveException;
use Quantum\Archive\Adapters\PharAdapter;
use Quantum\App\Exceptions\BaseException;
use Quantum\Archive\Adapters\ZipAdapter;
use Quantum\Archive\Enums\ArchiveType;
use Quantum\Archive\Archive;
use Quantum\Di\Di;

/**
 * Class ArchiveFactory
 * @package Quantum\Archive
 */
class ArchiveFactory
{
    public const ADAPTERS = [
        ArchiveType::PHAR => PharAdapter::class,
        ArchiveType::ZIP => ZipAdapter::class,
    ];

    /**
     * @var array<string, Archive>
     */
    private array $instances = [];

    /**
     * @throws BaseException
     */
    public static function get(string $type = ArchiveType::PHAR): Archive
    {
        return Di::get(self::class)->resolve($type);
    }

    /**
     * @throws BaseException
     */
    public function resolve(string $type = ArchiveType::PHAR): Archive
    {
        if (!isset($this->instances[$type])) {
            $this->instances[$type] = $this->createInstance($type);
        }

        return $this->instances[$type];
    }

    /**
     * @throws BaseException
     */
    private function createInstance(string $type): Archive
    {
        if (!isset(self::ADAPTERS[$type])) {
            throw ArchiveException::adapterNotSupported($type);
        }

        $adapterClass = self::ADAPTERS[$type];

        return new Archive(new $adapterClass());
    }
}
