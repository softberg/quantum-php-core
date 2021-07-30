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
 * @since 2.5.0
 */

namespace Quantum\Libraries\Storage;

/**
 * Class FileSystem
 * @package Quantum\Libraries\Storage
 */
class FileSystem
{

    /**
     * Is File
     * @param string $filename
     * @return bool
     */
    public function isFile(string $filename): bool
    {
        return is_file($filename);
    }

    /**
     * Is Directory
     * @param string $directory
     * @return bool
     */
    public function isDirectory(string $dirname): bool
    {
        return is_dir($dirname);
    }

    /**
     * Is Readable
     * @param string $filename
     * @return bool
     */
    public function isReadable(string $filename): bool
    {
        return is_readable($filename);
    }

    /**
     * Is Writable
     * @param string $filename
     * @return bool
     */
    public function isWritable(string $filename): bool
    {
        return is_writable($filename);
    }

    /**
     * File Exists
     * @param string $filename
     * @return bool
     */
    public function exists(string $filename): bool
    {
        return file_exists($filename) && is_file($filename);
    }

    /**
     * Gets the file content
     * @param string $filename
     * @return false|string
     */
    public function get(string $filename)
    {
        return file_get_contents($filename);
    }

    /**
     * Gets the content between given lines
     * @param string $filename
     * @param int $offset
     * @param int|null $length
     * @param int $flags
     * @return array
     */
    public function getLines(string $filename, int $offset, ?int $length, int $flags = 0): array
    {
        return array_slice(file($filename, $flags), $offset, $length, true);
    }

    /**
     * Gets the file size
     * @param string $filename
     * @return int|false
     */
    public function size(string $filename)
    {
        return filesize($filename);
    }

    /**
     * Gets the base name
     * @param string $path
     * @return string
     */
    public function baseName(string $path): string
    {
        return (string)pathinfo($path, PATHINFO_BASENAME);
    }

    /**
     * Gets the file name
     * @param string $path
     * @return string
     */
    public function fileName(string $path): string
    {
        return (string)pathinfo($path, PATHINFO_FILENAME);
    }

    /**
     * Gets the file extension
     * @param string $path
     * @return string
     */
    public function extension(string $path): string
    {
        return (string)pathinfo($path, PATHINFO_EXTENSION);
    }

    /**
     * Puts the data into the file
     * @param string $filename
     * @param string $data
     * @param bool $lock
     * @return false|int
     */
    public function put(string $filename, string $data, bool $lock = false)
    {
        return file_put_contents($filename, $data, $lock ? LOCK_EX : 0);
    }

    /**
     * Appends rge data at the end of the file
     * @param string $filename
     * @param string $data
     * @param bool $lock
     * @return int|false
     */
    public function append(string $filename, string $data, bool $lock = false)
    {
        return file_put_contents($filename, $data, $lock ? FILE_APPEND | LOCK_EX : FILE_APPEND);
    }

    /**
     * Renames the file
     * @param string $oldName
     * @param string $newName
     * @return bool
     */
    public function rename(string $oldName, string $newName): bool
    {
        return rename($oldName, $newName);
    }

    /**
     * Copy the file to destination
     * @param string $source
     * @param string $dest
     * @return bool
     */
    public function copy(string $source, string $dest): bool
    {
        return copy($source, $dest);
    }

    /**
     * Created new empty file
     * @param string $filename
     * @return bool
     */
    public function create(string $filename): bool
    {
        return touch($filename);
    }

    /**
     * Makes a new directory
     * @param string $path
     * @param int $mode
     * @param bool $recursive
     * @return bool
     */
    public function makeDirectory(string $path, int $mode = 0777, bool $recursive = false): bool
    {
        return mkdir($path, $mode, $recursive);
    }

    /**
     * Find pathnames matching a pattern
     * @param string $pattern
     * @param int $flags
     * @return array|false
     */
    public function glob(string $pattern, int $flags = 0)
    {
        return glob($pattern, $flags);
    }

    /**
     * Removes the file
     * @param string $filename
     * @return bool
     */
    public function remove(string $filename): bool
    {
        return unlink($filename);
    }

}
