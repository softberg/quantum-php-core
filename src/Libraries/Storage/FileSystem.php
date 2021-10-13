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
 */
class FileSystem
{

    /**
     * @var \Quantum\Libraries\Storage\FilesystemAdapterInterface|null
     */
    private $adapter;

    /**
     * FileSystem constructor
     * @param \Quantum\Libraries\Storage\FilesystemAdapterInterface|null $filesystemAdapter
     */
    public function __construct(?FilesystemAdapterInterface $filesystemAdapter = null)
    {
        if ($filesystemAdapter) {
            $this->adapter = $filesystemAdapter;
        } else {
            $this->adapter = LocalFileSystemAdapter::getInstance();
        }
    }

    /**
     * Gets the current adapter
     * @return \Quantum\Libraries\Storage\FilesystemAdapterInterface|null
     */
    public function getAdapter(): ?FilesystemAdapterInterface
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
