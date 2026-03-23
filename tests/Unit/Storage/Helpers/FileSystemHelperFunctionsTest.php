<?php

namespace Quantum\Tests\Unit\Storage\Helpers;

use Quantum\Storage\Adapters\GoogleDrive\GoogleDriveFileSystemAdapter;
use Quantum\Storage\Adapters\Dropbox\DropboxFileSystemAdapter;
use Quantum\Storage\Adapters\Local\LocalFileSystemAdapter;
use Quantum\Storage\Enums\FileSystemType;
use Quantum\Tests\Unit\AppTestCase;
use Quantum\Storage\FileSystem;

class FileSystemHelperFunctionsTest extends AppTestCase
{
    public function testFileSystemHelperGetDefaultFileSystem(): void
    {
        $this->assertInstanceOf(FileSystem::class, fs());

        $this->assertInstanceOf(LocalFileSystemAdapter::class, fs()->getAdapter());
    }

    public function testFileSystemHelperGetLocalFileSystem(): void
    {
        $this->assertInstanceOf(FileSystem::class, fs(FileSystemType::LOCAL));

        $this->assertInstanceOf(LocalFileSystemAdapter::class, fs(FileSystemType::LOCAL)->getAdapter());
    }

    public function testFileSystemHelperGetDropboxFileSystem(): void
    {
        $this->assertInstanceOf(FileSystem::class, fs(FileSystemType::DROPBOX));

        $this->assertInstanceOf(DropboxFileSystemAdapter::class, fs(FileSystemType::DROPBOX)->getAdapter());
    }

    public function testFileSystemHelperGetGoogleDriveFileSystem(): void
    {
        $this->assertInstanceOf(FileSystem::class, fs(FileSystemType::GDRIVE));

        $this->assertInstanceOf(GoogleDriveFileSystemAdapter::class, fs(FileSystemType::GDRIVE)->getAdapter());
    }
}
