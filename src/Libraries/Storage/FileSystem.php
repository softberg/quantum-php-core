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
 * @since 2.8.0
 */

namespace Quantum\Libraries\Storage;

use Quantum\Exceptions\FileSystemException;

/**
 * Class FileSystem
 * @package Quantum\Libraries\Storage
 * @method bool makeDirectory(string $dirname)
 * @method bool removeDirectory(string $dirname)
 * @method string|false get(string $filename)
 * @method int|false put(string $filename, string $content)
 * @method int|false append(string $filename, string $content)
 * @method bool rename(string $oldName, string $newName)
 * @method bool copy(string $source, string $dest)
 * @method bool exists(string $filename)
 * @method int|false size(string $filename)
 * @method int|false lastModified(string $filename)
 * @method bool remove(string $filename)
 * @method bool isFile(string $filename)
 * @method bool isDirectory(string $dirname)
 * @method string fileName(string $path)
 * @method string extension(string $path)
 * @method bool isReadable(string $filename)
 * @method bool isWritable(string $filename)
 * @method glob(string $pattern, int $flags = 0)
 * @method void require(string $file, bool $once = false)
 * @method void include(string $file, bool $once = false)
 */
class FileSystem
{

    /**
     * @var \Quantum\Libraries\Storage\FilesystemAdapterInterface
     */
    private $adapter;

    /**
     * FileSystem constructor
     * @param \Quantum\Libraries\Storage\FilesystemAdapterInterface|null $filesystemAdapter
     */
    public function __construct(FilesystemAdapterInterface $filesystemAdapter = null)
    {
        if ($filesystemAdapter) {
            $this->adapter = $filesystemAdapter;
        } else {
            $this->adapter = LocalFileSystemAdapter::getInstance();
        }
    }

    /**
     * Gets the current adapter
     * @return \Quantum\Libraries\Storage\FilesystemAdapterInterface
     */
    public function getAdapter(): FilesystemAdapterInterface
    {
        return $this->adapter;
    }

    /**
     * @param string $method
     * @param array|null $arguments
     * @return mixed
     * @throws \Quantum\Exceptions\FileSystemException
     */
    public function __call(string $method, ?array $arguments)
    {
        if (!method_exists($this->adapter, $method)) {
            throw FileSystemException::methodNotSupported($method, get_class($this->adapter));
        }

        return $this->adapter->$method(...$arguments);
    }

}
