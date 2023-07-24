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

namespace Quantum\Libraries\Archive\Adapters;

use Quantum\Libraries\Archive\ArchiveInterface;
use Quantum\Libraries\Storage\FileSystem;
use Quantum\Exceptions\ArchiveException;
use Quantum\Exceptions\LangException;
use Exception;
use Phar;

/**
 * Class PharAdapter
 * @package Quantum\Libraries\Archive\Adapters
 */
class PharAdapter implements ArchiveInterface
{

    /**
     * @var FileSystem
     */
    private $fs;

    /**
     * @var Phar
     */
    private $archive;

    /**
     * @var string
     */
    private $archiveName;

    /**
     * Phar constructor
     */
    public function __construct(string $archiveName)
    {
        $this->archiveName = $archiveName;

        $this->fs = new FileSystem();
        $this->archive = new Phar($archiveName);
    }

    public function removeArchive(): bool
    {
        try {
            $this->archive = null;
            return Phar::unlinkArchive($this->archiveName);
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * @inheritDoc
     */
    public function offsetExists(string $filename): bool
    {
        return $this->archive->offsetExists($filename);
    }

    /**
     * @inheritDoc
     */
    public function addEmptyDir(string $directory): bool
    {
        if (!$this->offsetExists($directory)) {
            $this->archive->addEmptyDir($directory);
            return true;
        }

        return false;
    }

    /**
     * @inheritDoc
     * @throws ArchiveException
     * @throws LangException
     */
    public function addFile(string $filePath, string $entryName = null): bool
    {
        if (!$this->fs->exists($filePath)) {
            throw ArchiveException::fileNotFound($filePath);
        }

        try {
            $this->archive->addFile($filePath, $entryName);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * @inheritDoc
     */
    public function addFromString(string $entryName, string $content): bool
    {
        try {
            $this->archive->addFromString($entryName, $content);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * @inheritDoc
     */
    public function addMultipleFiles(array $fileNames): bool
    {
        try {
            foreach ($fileNames as $fileName => $filePath) {
                $this->archive->addFile($filePath, $fileName);
            }
        } catch (Exception $e) {
            return false;
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function count(): int
    {
        return $this->archive->count();
    }

    /**
     * @inheritDoc
     */
    public function extractTo(string $pathToExtract, $files = null): bool
    {
        try {
            $this->archive->extractTo($pathToExtract, $files);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * @inheritDoc
     */
    public function deleteFile(string $filename): bool
    {
        try {
            $this->archive->delete($filename);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * @inheritDoc
     */
    public function deleteMultipleFiles(array $fileNames): bool
    {
        try {
            foreach ($fileNames as $key => $fileName) {
                $this->archive->delete($fileName);
            }
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
