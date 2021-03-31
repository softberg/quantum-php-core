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
 * @since 1.9.0
 */

namespace Quantum\Libraries\Storage;

/**
 * Class FileSystem
 *
 * @package Quantum\Libraries\Storage
 */
class FileSystem
{

    /**
     * Is File
     *
     * @param string $file
     * @return bool
     */
    public function isFile($file)
    {
        return is_file($file);
    }

    /**
     * Is Directory
     *
     * @param string $directory
     * @return bool
     */
    public function isDirectory($directory)
    {
        return is_dir($directory);
    }

    /**
     * Is Readable
     *
     * @param string $file
     * @return bool
     */
    public function isReadable($file)
    {
        return is_readable($file);
    }

    /**
     * Is Writable
     *
     * @param string $file
     * @return bool
     */
    public function isWritable($file)
    {
        return is_writable($file);
    }

    /**
     * Exists
     *
     * @param string $file
     * @return bool
     */
    public function exists($file)
    {
        return file_exists($file);
    }

    /**
     * Get
     *
     * @param string $file
     * @return false|string
     */
    public function get($file)
    {
        return file_get_contents($file);
    }

    /**
     * Size
     *
     * @param string $file
     * @return false|int
     */
    public function size($file)
    {
        return filesize($file);
    }

    /**
     * Base name 
     * 
     * @param string $file
     * @return string
     */
    public function baseName($file)
    {
        return (string) pathinfo($file, PATHINFO_BASENAME);
    }

    /**
     * File name
     * 
     * @param string $file
     * @return string
     */
    public function fileName($file)
    {
        return (string) pathinfo($file, PATHINFO_FILENAME);
    }

    /**
     * Extension
     *
     * @param string $file
     * @return string
     */
    public function extension($file)
    {
        return (string) pathinfo($file, PATHINFO_EXTENSION);
    }

    /**
     * Put
     * @param string $file
     * @param string $data
     * @param int $flag
     * @return false|int
     */
    public function put($file, $data, $flag = 0)
    {
        return file_put_contents($file, $data, $flag);
    }

    /**
     * Append
     *
     * @param string $file
     * @param string $data
     * @param bool $lock
     * @return false|int
     */
    public function append($file, $data, $lock = false)
    {
        return file_put_contents($file, $data, $lock ? FILE_APPEND | LOCK_EX : FILE_APPEND);
    }

    /**
     * Rename
     *
     * @param string $oldName
     * @param string $newName
     * @return bool
     */
    public function rename($oldName, $newName)
    {
        return rename($oldName, $newName);
    }

    /**
     * Copy
     *
     * @param string $source
     * @param string $dest
     * @return bool
     */
    public function copy($source, $dest)
    {
        return copy($source, $dest);
    }

    /**
     * Make Directory
     *
     * @param string $path
     * @param int $mode
     * @param bool $recursive
     * @return bool
     */
    public function makeDirectory($path, $mode = 0777, $recursive = false)
    {
        return mkdir($path, $mode, $recursive);
    }

    /**
     * Glob
     *
     * @param string $pattern
     * @param int $flags
     * @return array|false
     */
    public function glob($pattern, $flags = 0)
    {
        return glob($pattern, $flags);
    }

    /**
     * Remove
     *
     * @param string $file
     * @return bool
     */
    public function remove($file)
    {
        return unlink($file);
    }

}
