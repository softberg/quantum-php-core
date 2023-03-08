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
    /**
     * @var ZipArchive
     */
    private $zipArchive;

    /**
     * @var string
     */
    private $archiveName;

    /**
     * Zip constructor
     */
    public function __construct(string $archiveName)
    {
        $this->zipArchive = new ZipArchive();
        $this->archiveName = $archiveName;
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
    public function offsetExists(string $fileOrDirName): bool
    {
        
        if($this->zipArchive->open($this->archiveName, ZipArchive::CREATE) === TRUE) {
            if (strpos($fileOrDirName, '.') === false) {
                $fileOrDirName = rtrim($fileOrDirName, '/') . '/';
            }
            return $this->zipArchive->locateName($fileOrDirName) !== false;
        } 
    }

    /**
     * @inheritDoc
     */
    public function addEmptyDir(string $newDirectory): bool
    {
        if ($this->zipArchive->open($this->archiveName, ZipArchive::CREATE) === TRUE) {
            if (!$this->offsetExists($newDirectory)) {
                return $this->zipArchive->addEmptyDir($newDirectory);
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * @inheritDoc
     */
    public function addFile(string $filePath, string $newFileName = ''): bool
    {
        if ($this->zipArchive->open($this->archiveName, ZipArchive::CREATE) === TRUE) {
            return $this->zipArchive->addFile($filePath, $newFileName);
        } else {
            return false;
        }
    }

    /**
     * @inheritDoc
     */
    public function addFromString(string $newFileName, string $newFileContent): bool
    {
        if ($this->zipArchive->open($this->archiveName, ZipArchive::CREATE) === TRUE) {
            return $this->zipArchive->addFromString($newFileName, $newFileContent);
        } else {
            return false;
        }
    }

    /**
     * @inheritDoc
     */
    public function deleteUsingName(string $fileOrDirName): bool
    {
        if (
            $this->zipArchive->open($this->archiveName) === TRUE
            && $this->offsetExists($fileOrDirName)
        ) {
            return $this->zipArchive->deleteName($fileOrDirName);
        } else {
            return false;
        }
    }

    /**
     * @inheritDoc
     */
    public function extractTo(string $pathToExtract, $files = ''): bool
    {
        if ($this->zipArchive->open($this->archiveName) === TRUE) {
            return $this->zipArchive->extractTo($pathToExtract);
        } else {
            return false;
        }
    }

    /**
     * @inheritDoc
     */
    public function count(): int
    {
        return $this->zipArchive->count();
    }

    /**
     * @inheritDoc
     */
    public function addMultipleFiles(array $fileNames): bool
    {
        if ($this->zipArchive->open($this->archiveName, ZipArchive::CREATE) === TRUE) {
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
    public function deleteMultipleFilesUsingName(array $fileNames): bool
    {
        if ($this->zipArchive->open($this->archiveName) === TRUE) {
            foreach ($fileNames as $key => $fileOrDirName) {
                $this->zipArchive->deleteName($fileOrDirName);
            }
            return true;
        } else {
            return false;
        }
    }
}
