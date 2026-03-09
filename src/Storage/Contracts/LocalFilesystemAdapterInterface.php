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
 * @since 3.0.0
 */

namespace Quantum\Storage\Contracts;

/**
 * Interface LocalFilesystemAdapterInterface
 * @package Quantum\Storage
 */
interface LocalFilesystemAdapterInterface extends FilesystemAdapterInterface
{
    /**
     * Find path names matching a pattern
     * @return array|false
     */
    public function glob(string $pattern, int $flags = 0);

    /**
     * Is Readable
     */
    public function isReadable(string $filename): bool;

    /**
     * Is Writable
     */
    public function isWritable(string $filename): bool;

    /**
     * Gets the content between given lines
     */
    public function getLines(string $filename, int $offset = 0, ?int $length = null): array;

    /**
     * Gets the filename with extension
     */
    public function fileNameWithExtension(string $path): string;

    /**
     * Gets the file name
     */
    public function fileName(string $path): string;

    /**
     * Gets the file extension
     */
    public function extension(string $path): string;

    /**
     * Includes the required file
     * @return mixed
     */
    public function require(string $file, bool $once = false);

    /**
     * Includes a file
     * @return mixed
     */
    public function include(string $file, bool $once = false);
}
