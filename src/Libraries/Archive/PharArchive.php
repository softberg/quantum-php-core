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

namespace Quantum\Libraries\Archive;

use Phar;

/**
 * Class ArchiveInterface
 * @package Quantum\Libraries\Archive
 */
class PharArchive implements ArchiveInterface
{

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
    }

    /**
     * @inheritDoc
     */
    public function removeArchive(): bool
    {
        try {
            return Phar::unlinkArchive($this->archiveName);
        } catch (\Throwable $th) {
            return false;
        }
    }

    /**
     * @inheritDoc
     */
    public function offsetExists(string $fileOrDirName): bool
    {
        $newPhar = new Phar($this->archiveName);
        return $newPhar->offsetExists($fileOrDirName);
    }

    /**
     * @inheritDoc
     */
    public function addEmptyDir(string $newDirectory): bool
    {
        try {
            $newPhar = new Phar($this->archiveName);
            $newPhar->addEmptyDir($newDirectory);
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }

    /**
     * @inheritDoc
     */
    public function addFile(string $newFilePath, string $newFileName = ''): bool
    {
        try {
            $newPhar = new Phar($this->archiveName);
            $newPhar->addFile($newFilePath, $newFileName);
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }

    /**
     * @inheritDoc
     */
    public function addFromString(string $newFileName, string $newFileContent): bool
    {
        try {
            $newPhar = new Phar($this->archiveName);
            $newPhar->addFromString($newFileName, $newFileContent);
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }

    /**
     * @inheritDoc
     */
    public function deleteUsingName(string $fileName): bool
    {
        try {
            $newPhar = new Phar($this->archiveName);
            $newPhar->delete($fileName);
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }

    /**
     * @inheritDoc
     */
    public function extractTo(string $pathToExtract, $files = ''): bool
    {

        try {
            $newPhar = new Phar($this->archiveName);
            $newPhar->extractTo($pathToExtract, $files);
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }

    /**
     * @inheritDoc
     */
    public function count(): int
    {
        $newPhar = new Phar($this->archiveName);
        $pharCount = $newPhar->count();
        return $pharCount;
    }

    /**
     * @inheritDoc
     */
    public function addMultipleFiles(array $fileNames): bool
    {
        try {
            $newPhar = new Phar($this->archiveName);
            foreach ($fileNames as $fileName => $filePath) {
                $newPhar->addFile($filePath, $fileName);
            }
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }

    /**
     * @inheritDoc
     */
    public function deleteMultipleFilesUsingName(array $fileNames): bool
    {
        try {
            $newPhar = new Phar($this->archiveName);
            foreach ($fileNames as $key => $fileName) {
                $newPhar->delete($fileName);
            }
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }
}
