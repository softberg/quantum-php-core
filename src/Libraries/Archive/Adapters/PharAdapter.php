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
use Exception;
use Phar;

/**
 * Class PharAdapter
 * @package Quantum\Libraries\Archive
 */
class PharAdapter implements ArchiveInterface
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
     * @var Phar
     */
    private $archive = null;

    /**
     * @var false
     */
    private $requiresReopen = true;

    /**
     * @throws BaseException
     */
    public function __construct()
    {
        $this->fs = FileSystemFactory::get();
    }

    public function setName(string $archiveName): void
    {
        $this->archiveName = $archiveName;
    }

    /**
     * @return bool
     * @throws ArchiveException
     */
    public function removeArchive(): bool
    {
        $this->ensureArchiveOpen();

        try {
            $this->archive = null;
            return Phar::unlinkArchive($this->archiveName);
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * @inheritDoc
     * @throws ArchiveException
     */
    public function offsetExists(string $filename): bool
    {
        $this->ensureArchiveOpen();

        return $this->archive->offsetExists($filename);
    }

    /**
     * @inheritDoc
     * @throws ArchiveException
     */
    public function addEmptyDir(string $directory): bool
    {
        $this->ensureArchiveOpen();

        if (!$this->offsetExists($directory)) {
            $this->archive->addEmptyDir($directory);
            return true;
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

        try {
            $this->archive->addFile($filePath, $entryName);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * @inheritDoc
     * @throws ArchiveException
     */
    public function addFromString(string $entryName, string $content): bool
    {
        $this->ensureArchiveOpen();

        try {
            $this->archive->addFromString($entryName, $content);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * @inheritDoc
     * @throws ArchiveException
     */
    public function addMultipleFiles(array $fileNames): bool
    {
        $this->ensureArchiveOpen();

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

        try {
            $this->archive->extractTo($pathToExtract, $files);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * @inheritDoc
     * @throws ArchiveException
     */
    public function deleteFile(string $filename): bool
    {
        $this->ensureArchiveOpen();

        try {
            $this->archive->delete($filename);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * @inheritDoc
     * @throws ArchiveException
     */
    public function deleteMultipleFiles(array $fileNames): bool
    {
        $this->ensureArchiveOpen();

        try {
            foreach ($fileNames as $fileName) {
                $this->archive->delete($fileName);
            }

            return true;
        } catch (Exception $e) {
            return false;
        }
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
    private function openArchive(): void
    {
        if (empty($this->archiveName)) {
            throw ArchiveException::missingArchiveName();
        }

        if ($this->archive === null) {
            try {
                $this->archive = new Phar($this->archiveName);
            } catch (Exception $e) {
                throw ArchiveException::cantOpen($this->archiveName);
            }
        }

        $this->requiresReopen = false;
    }
}