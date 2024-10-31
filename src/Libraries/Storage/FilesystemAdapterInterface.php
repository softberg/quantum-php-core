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
 * @since 2.9.0
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
     * @param string|null $parentId
     * @return bool
     */
    public function makeDirectory(string $dirname, string $parentId = null): bool;

    /**
     * Removes the directory
     * @param string $dirname
     * @return bool
     */
    public function removeDirectory(string $dirname): bool;

    /**
     * Gets the file content
     * @param string $filename
     * @return string|false
     */
    public function get(string $filename);

    /**
     * Puts the content into the file
     * @param string $filename
     * @param string $content
     * @param string|null $parentId
     * @return int|false
     */
    public function put(string $filename, string $content, string $parentId = null);

    /**
     * Appends the content at the end of the file
     * @param string $filename
     * @param string $content
     * @return int|false
     */
    public function append(string $filename, string $content);

    /**
     * Renames the file
     * @param string $oldName
     * @param string $newName
     * @return bool
     */
    public function rename(string $oldName, string $newName): bool;

    /**
     * Copy the file to destination
     * @param string $source
     * @param string $dest
     * @return bool
     */
    public function copy(string $source, string $dest): bool;

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
     * @return bool
     */
    public function remove(string $filename): bool;

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

    /**
     * Lists the files inside the directory
     * @param string $dirname
     * @return array|bool
     */
    public function listDirectory(string $dirname);

}