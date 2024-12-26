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

use Quantum\Libraries\Archive\ArchiveException;
use Quantum\Libraries\Archive\ArchiveInterface;
use Quantum\Libraries\Storage\FileSystem;
use Quantum\Exceptions\AppException;
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
     * @var ZipArchive
     */
    private $archive;

    /**
     * @var string
     */
    private $archiveName;

    /**
     * @var int|null
     */
    private $mode;

    /**
     * @param string $archiveName
     * @param int|null $mode
     * @throws ArchiveException
     */
    public function __construct(string $archiveName, ?int $mode = ZipArchive::CREATE)
    {
        $this->archiveName = $archiveName;
        $this->mode = $mode;

        $this->fs = new FileSystem();
        $this->archive = new ZipArchive();

        $this->reopen();
    }

    /**
     * @inheritDoc
     */
    public function offsetExists(string $filename): bool
    {
        if (strpos($filename, '.') === false) {
            $filename = rtrim($filename, '/') . '/';
        }

        return $this->archive->locateName($filename) !== false;
    }

    /**
     * @inheritDoc
     */
    public function addEmptyDir(string $directory): bool
    {
        if (!$this->offsetExists($directory)) {
            return $this->archive->addEmptyDir($directory);
        }

        return false;
    }

    /**
     * @inheritDoc
     * @throws AppException
     */
    public function addFile(string $filePath, string $entryName = null): bool
    {
        if (!$this->fs->exists($filePath)) {
            throw ArchiveException::fileNotFound($filePath);
        }

        return $this->archive->addFile($filePath, $entryName);
    }

    /**
     * @inheritDoc
     */
    public function addFromString(string $entryName, string $content): bool
    {
        return $this->archive->addFromString($entryName, $content);
    }

    /**
     * @inheritDoc
     */
    public function addMultipleFiles(array $fileNames): bool
    {
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
     */
    public function count(): int
    {
        return $this->archive->count();
    }

    /**
     * @inheritDoc
     * @throws ArchiveException
     */
    public function extractTo(string $pathToExtract, $files = null): bool
    {
        $this->reopen();
        return $this->archive->extractTo($pathToExtract);
    }

    /**
     * @inheritDoc
     * @throws ArchiveException
     */
    public function deleteFile(string $filename): bool
    {
        if ($this->offsetExists($filename)) {
            $state = $this->archive->deleteName($filename);
            $this->reopen();
            return $state;
        }

        return false;
    }

    /**
     * @inheritDoc
     * @throws ArchiveException
     */
    public function deleteMultipleFiles(array $fileNames): bool
    {
        foreach ($fileNames as $entryName) {
            $this->deleteFile($entryName);
        }

        return true;
    }

    /**
     * @throws ArchiveException
     */
    private function reopen()
    {
        if ($this->archive->filename) {
            $this->archive->close();
        }

        if ($res = $this->archive->open($this->archiveName, $this->mode) !== true) {
            throw ArchiveException::cantOpen($res);
        }

    }
}
