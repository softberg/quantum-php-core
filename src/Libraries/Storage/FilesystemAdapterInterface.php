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
 * @since 2.6.0
 */

namespace Quantum\Libraries\Storage;

/**
 * Interface StorageInterface
 * @package Quantum\Libraries\Storage
 */
interface FilesystemAdapterInterface
{

    /**
     * Makes a new directory
     * @param string $dirname
     */
    public function makeDirectory(string $dirname);

    /**
     * Removes the directory
     * @param string $dirname
     */
    public function removeDirectory(string $dirname);

    /**
     * Gets the file content
     * @param string $filename
     * @return string
     */
    public function get(string $filename): string;

    /**
     * Puts the content into the file
     * @param string $filename
     * @param string $content
     */
    public function put(string $filename, string $content);

    /**
     * Appends the content at the end of the file
     * @param string $filename
     * @param string $content
     */
    public function append(string $filename, string $content);

    /**
     * Renames the file
     * @param string $oldName
     * @param string $newName
     */
    public function rename(string $oldName, string $newName);

    /**
     * Copy the file to destination
     * @param string $source
     * @param string $dest
     */
    public function copy(string $source, string $dest);

    /**
     * File Exists
     * @param string $filename
     * @return bool
     */
    public function exists(string $filename): bool;

    /**
     * Gets the file size
     * @param string $filename
     * @return int|false
     */
    public function size(string $filename);

    /**
     * Gets file modification time
     * @param string $filename
     * @return int|false
     */
    public function lastModified(string $filename);

    /**
     * Removes the file
     * @param string $filename
     */
    public function remove(string $filename);

    /**
     * Is File
     * @param string $filename
     * @return bool
     */
    public function isFile(string $filename): bool;

    /**
     * Is Directory
     * @param string $dirname
     * @return bool
     */
    public function isDirectory(string $dirname): bool;

}