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
 * @since 2.9.5
 */

namespace Quantum\Libraries\Archive\Contracts;

/**
 * Interface ArchiveInterface
 * @package Quantum\Libraries\Archive
 */
interface ArchiveInterface
{
    /**
     * Checks for the existence of a file
     * @param string $filename
     * @return bool
     */
    public function offsetExists(string $filename): bool;

    /**
     * Makes a new empty directory
     * @param string $directory
     * @return bool
     */
    public function addEmptyDir(string $directory): bool;

    /**
     * Adds new file to the archive
     * @param string $filePath
     * @param string $entryName
     * @return bool
     */
    public function addFile(string $filePath, string $entryName): bool;

    /**
     * Adds new file to the archive from string
     * @param string $entryName
     * @param string $content
     * @return bool
     */
    public function addFromString(string $entryName, string $content): bool;

    /**
     * Adds multiple files to the archive
     * @param array $fileNames
     * @return bool
     */
    public function addMultipleFiles(array $fileNames): bool;

    /**
     * Files count in the archive
     * @return int
     */
    public function count(): int;

    /**
     * Extracts the archive
     * @param string $pathToExtract
     * @param string|array $files
     * @return bool
     */
    public function extractTo(string $pathToExtract, $files = null): bool;

    /**
     * Delete the file from the archive
     * @param string $filename
     * @return bool
     */
    public function deleteFile(string $filename): bool;

    /**
     * Delete a multiple files
     * @param array $fileNames
     * @return bool
     */
    public function deleteMultipleFiles(array $fileNames): bool;
}