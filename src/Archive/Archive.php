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

namespace Quantum\Archive;

use Quantum\Archive\Exceptions\ArchiveException;
use Quantum\Archive\Contracts\ArchiveInterface;
use Quantum\App\Exceptions\BaseException;

/**
 * Class Archive
 * @package Quantum\Archive
 * @method void setName(string $archiveName)
 * @method bool offsetExists(string $filename)
 * @method bool addEmptyDir(string $directory)
 * @method bool addFile(string $filePath, string $entryName)
 * @method bool addFromString(string $entryName, string $content)
 * @method bool addMultipleFiles(array $fileNames)
 * @method int count()
 * @method bool extractTo(string $pathToExtract, $files = null)
 * @method bool deleteFile(string $filename)
 * @method bool deleteMultipleFiles(array $fileNames)
 */
class Archive
{
    /**
     * Phar
     */
    public const PHAR = 'phar';

    /**
     * Zip
     */
    public const ZIP = 'zip';

    private ArchiveInterface $adapter;

    public function __construct(ArchiveInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    public function getAdapter(): ArchiveInterface
    {
        return $this->adapter;
    }

    /**
     * @return mixed
     * @throws BaseException
     */
    public function __call(string $method, ?array $arguments)
    {
        if (!method_exists($this->adapter, $method)) {
            throw ArchiveException::methodNotSupported($method, get_class($this->adapter));
        }

        return $this->adapter->$method(...$arguments);
    }
}
