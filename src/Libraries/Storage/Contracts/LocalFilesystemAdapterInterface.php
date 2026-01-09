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

namespace Quantum\Libraries\Storage\Contracts;

/**
 * Interface LocalFilesystemAdapterInterface
 * @package Quantum\Libraries\Storage
 */
interface LocalFilesystemAdapterInterface extends FilesystemAdapterInterface
{
    /**
     * Find path names matching a pattern
     * @param string $pattern
     * @param int $flags
     * @return array|false
     */
    public function glob(string $pattern, int $flags = 0);

    /**
     * Is Readable
     * @param string $filename
     * @return bool
     */
    public function isReadable(string $filename): bool;

    /**
     * Is Writable
     * @param string $filename
     * @return bool
     */
    public function isWritable(string $filename): bool;

    /**
     * Gets the content between given lines
     * @param string $filename
     * @param int $offset
     * @param int|null $length
     * @return array
     */
    public function getLines(string $filename, int $offset = 0, ?int $length = null): array;

    /**
     * Gets the filename with extension
     * @param string $path
     * @return string
     */
    public function fileNameWithExtension(string $path): string;

    /**
     * Gets the file name
     * @param string $path
     * @return string
     */
    public function fileName(string $path): string;

    /**
     * Gets the file extension
     * @param string $path
     * @return string
     */
    public function extension(string $path): string;

    /**
     * Includes the required file
     * @param string $file
     * @param bool $once
     * @return mixed
     */
    public function require(string $file, bool $once = false);

    /**
     * Includes a file
     * @param string $file
     * @param bool $once
     * @return mixed
     */
    public function include(string $file, bool $once = false);
}
