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

use Quantum\Exceptions\FileSystemException;

/**
 * Class FileSystem
 * @package Quantum\Libraries\Storage
 * @method void makeDirectory(string $dirname)
 * @method void removeDirectory(string $dirname)
 * @method string get(string $filename)
 * @method void put(string $filename, string $content)
 * @method void append(string $filename, string $content)
 * @method void rename(string $oldName, string $newName)
 * @method void copy(string $source, string $dest)
 * @method bool exists(string $filename)
 * @method int|false size(string $filename)
 * @method int|false lastModified(string $filename)
 * @method void remove(string $filename)
 * @method bool isFile(string $filename)
 * @method bool isDirectory(string $dirname)
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
