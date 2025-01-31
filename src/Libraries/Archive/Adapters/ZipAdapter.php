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

namespace Quantum\Libraries\Archive\Adapters;

use Quantum\Libraries\Archive\Exceptions\ArchiveException;
use Quantum\Libraries\Storage\Factories\FileSystemFactory;
use Quantum\Libraries\Archive\Contracts\ArchiveInterface;
use Quantum\Libraries\Storage\FileSystem;
use Quantum\Exceptions\BaseException;
use ZipArchive;
use Exception;

/**
 * Class ZipAdapter
 * @package Quantum\Libraries\Archive\Adapters
 */
class ZipAdapter implements ArchiveInterface
{

    /**
     * @var FileSystem
     */
    private $fs;

    /**
     * @var string
     */
    private $archiveName;

    /**
     * @var ZipArchive
     */
    private $archive = null;

    /**
     * @var bool
     */
    private $requiresReopen = true;


    /**
     * @throws BaseException
     */
    public function __construct()
    {
        $this->fs = FileSystemFactory::get();
    }

    /**
     * @param string $archiveName
     */
    public function setName(string $archiveName)
    {
        $this->archiveName = $archiveName;
    }

    /**
     * @inheritDoc
     * @throws ArchiveException
     */
    public function offsetExists(string $filename): bool
    {
        $this->ensureArchiveOpen();

        if (strpos($filename, '.') === false) {
            $filename = rtrim($filename, '/') . '/';
        }

        return $this->archive->locateName($filename) !== false;
    }

    /**
     * @inheritDoc
     * @throws ArchiveException
     */
    public function addEmptyDir(string $directory): bool
    {
        $this->ensureArchiveOpen();

        if (!$this->offsetExists($directory)) {
            return $this->archive->addEmptyDir($directory);
        }

        return false;
    }

    /**
     * @inheritDoc
     * @throws BaseException
     */
    public function addFile(string $filePath, string $entryName = null): bool
    {
        $this->ensureArchiveOpen();

        if (!$this->fs->exists($filePath)) {
            throw ArchiveException::fileNotFound($filePath);
        }

        $result = $this->archive->addFile($filePath, $entryName);
        $this->requiresReopen = true;

        return $result;
    }

    /**
     * @inheritDoc
     * @throws ArchiveException
     */
    public function addFromString(string $entryName, string $content): bool
    {
        $this->ensureArchiveOpen();

        $result = $this->archive->addFromString($entryName, $content);
        $this->requiresReopen = true;

        return $result;
    }

    /**
     * @inheritDoc
     * @throws ArchiveException
     */
    public function addMultipleFiles(array $fileNames): bool
    {
        $this->ensureArchiveOpen();

        try {
            foreach ($fileNames as $entryName => $filePath) {
                $this->addFile($filePath, $entryName);
            }
        } catch (Exception $e) {
            return false;
        }

        return true;
    }

    /**
     * @inheritDoc
     * @throws ArchiveException
     */
    public function count(): int
    {
        $this->ensureArchiveOpen();

        return $this->archive->count();
    }

    /**
     * @inheritDoc
     * @throws ArchiveException
     */
    public function extractTo(string $pathToExtract, $files = null): bool
    {
        $this->ensureArchiveOpen();

        return $this->archive->extractTo($pathToExtract);
    }

    /**
     * @inheritDoc
     * @throws ArchiveException
     */
    public function deleteFile(string $filename): bool
    {
        $this->ensureArchiveOpen();

        if ($this->offsetExists($filename)) {
            $result = $this->archive->deleteName($filename);
            $this->requiresReopen = true;

            return $result;
        }

        return false;
    }

    /**
     * @inheritDoc
     * @throws ArchiveException
     */
    public function deleteMultipleFiles(array $fileNames): bool
    {
        $this->ensureArchiveOpen();

        foreach ($fileNames as $entryName) {
            if (!$this->deleteFile($entryName)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @throws ArchiveException
     */
    private function ensureArchiveOpen(): void
    {
        if ($this->requiresReopen || $this->archive === null) {
            $this->openArchive();
        }
    }

    /**
     * @throws ArchiveException
     */
    private function openArchive()
    {
        if (empty($this->archiveName)) {
            throw ArchiveException::missingArchiveName();
        }

        if ($this->archive === null) {
            $this->archive = new ZipArchive();
        }

        if ($this->archive->filename) {
            $this->archive->close();
        }

        if ($this->archive->open($this->archiveName, ZipArchive::CREATE) !== true) {
            throw ArchiveException::cantOpen($this->archiveName);
        }

        $this->requiresReopen = false;
    }
}