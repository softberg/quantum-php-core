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

namespace Quantum\Libraries\Archive;

use ZipArchive;

/**
 * Class ArchiveInterface
 * @package Quantum\Libraries\Archive
 */
class Zip implements ArchiveInterface
{
    private $zipArchive;

    /**
     * Zip constructor
     */
    public function __construct()
    {
        $this->zipArchive = new ZipArchive();
    }

    public function __destruct()
    {
        if ($this->zipArchive->filename) {
            $this->zipArchive->close();
        }
    }

    /**
     * @inheritDoc
     */
    public function addEmptyDir(string $archiveName, string $newDirectory): bool
    {
        if ($this->zipArchive->open($archiveName, ZipArchive::CREATE) === TRUE) {
            return $this->zipArchive->addEmptyDir($newDirectory);
        } else {
            return false;
        }
    }

    /**
     * @inheritDoc
     */
    public function addFile(string $archiveName, string $filePath, string $newFileName = ''): bool
    {
        if ($this->zipArchive->open($archiveName, ZipArchive::CREATE) === TRUE) {
            return $this->zipArchive->addFile($filePath, $newFileName);
        } else {
            return false;
        }
    }

    /**
     * @inheritDoc
     */
    public function addFromString(string $archiveName, string $newFileName, string $newFileContent): bool
    {
        if ($this->zipArchive->open($archiveName, ZipArchive::CREATE) === TRUE) {
            return $this->zipArchive->addFromString($newFileName, $newFileContent);
        } else {
            return false;
        }
    }

    /**
     * @inheritDoc
     */
    public function deleteUsingName(string $archiveName, string $fileOrDirName): bool
    {
        if ($this->zipArchive->open($archiveName) === TRUE && $this->zipArchive->locateName($fileOrDirName)) {
            return $this->zipArchive->deleteName($fileOrDirName);
        } else {
            return false;
        }
    }

    /**
     * @inheritDoc
     */
    public function extractTo(string $archiveName, string $pathToExtract): bool
    {
        if ($this->zipArchive->open($archiveName) === TRUE) {
            return $this->zipArchive->extractTo($pathToExtract);
        } else {
            return false;
        }
    }

    /**
     * @inheritDoc
     */
    public function renameUsingName(string $archiveName, string $currentName, string $newName): bool
    {
        if ($this->zipArchive->open($archiveName) === TRUE) {
            return $this->zipArchive->renameName($currentName, $newName);
        } else {
            return false;
        }
    }

    /**
     * @inheritDoc
     */
    public function addMultipleFiles(string $archiveName, array $fileNames): bool
    {
        if ($this->zipArchive->open($archiveName) === TRUE) {
            foreach ($fileNames as $fileNmae => $filePath) {
                if (!$this->zipArchive->addFile($filePath, $fileNmae)) {
                    return false;
                }
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * @inheritDoc
     */
    public function deleteMultipleFilesUsingName(string $archiveName, array $fileNames): bool
    {
        if ($this->zipArchive->open($archiveName) === TRUE) {
            foreach ($fileNames as $fileOrDirName => $filePath) {
                $this->zipArchive->deleteName($fileOrDirName);
            }
            return true;
        } else {
            return false;
        }
    }
}
