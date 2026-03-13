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

namespace Quantum\Archive\Contracts;

/**
 * Interface ArchiveInterface
 * @package Quantum\Archive
 */
interface ArchiveInterface
{
    /**
     * Checks for the existence of a file
     */
    public function offsetExists(string $filename): bool;

    /**
     * Makes a new empty directory
     */
    public function addEmptyDir(string $directory): bool;

    /**
     * Adds new file to the archive
     */
    public function addFile(string $filePath, string $entryName): bool;

    /**
     * Adds new file to the archive from string
     */
    public function addFromString(string $entryName, string $content): bool;

    /**
     * Adds multiple files to the archive
     */
    public function addMultipleFiles(array $fileNames): bool;

    /**
     * Files count in the archive
     */
    public function count(): int;

    /**
     * Extracts the archive
     * @param string|array $files
     */
    public function extractTo(string $pathToExtract, $files = null): bool;

    /**
     * Delete the file from the archive
     */
    public function deleteFile(string $filename): bool;

    /**
     * Delete a multiple files
     */
    public function deleteMultipleFiles(array $fileNames): bool;
}
