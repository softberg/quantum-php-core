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

/**
 * Interface StorageInterface
 * @package Quantum\Libraries\Archive
 */
interface ArchiveInterface
{
    /**
     * Makes a new directory
     * @param string $archiveName
     * @param string $newDirectory
     * @return bool
     */
    public function addEmptyDir(string $archiveName, string $newDirectory): bool;

    /**
     * Makes a new directory
     * @param string $archiveName
     * @param string $filePath
     * @param string $newFileName
     * @return bool
     */
    public function addFile(string $archiveName, string $filePath, string $newFileName): bool;

    /**
     * Makes a new directory
     * @param string $archiveName
     * @param string $newFileName
     * @param string $newFileContent
     * @return bool
     */
    public function addFromString(string $archiveName, string $newFileName, string $newFileContent): bool;

    
    /**
     * Makes a new directory
     * @param string $archiveName
     * @param string $fileOrDirName
     * @return bool
     */
    public function deleteUsingName(string $archiveName, string $fileOrDirName): bool;

    
    /**
     * Makes a new directory
     * @param string $archiveName
     * @param string $pathToExtract
     * @return bool
     */
    public function extractTo(string $archiveName, string $pathToExtract): bool;

    
    /**
     * Makes a new directory
     * @param string $archiveName
     * @param string $currentName
     * @param string $newName
     * @return bool
     */
    public function renameUsingName(string $archiveName, string $currentName, string $newName): bool;
    
    /**
     * Makes a new directory
     * @param string $archiveName
     * @param array $fileNames
     * @return bool
     */
    public function addMultipleFiles(string $archiveName, array $fileNames): bool;
    
    /**
     * Makes a new directory
     * @param string $archiveName
     * @param array $fileNames
     * @return bool
     */
    public function deleteMultipleFilesUsingName(string $archiveName, array $fileNames): bool;

}