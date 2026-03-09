<?php

namespace Libraries\Storage\Helpers;

use Quantum\Storage\Adapters\GoogleDrive\GoogleDriveFileSystemAdapter;
use Quantum\Storage\Adapters\Dropbox\DropboxFileSystemAdapter;
use Quantum\Storage\Adapters\Local\LocalFileSystemAdapter;
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
        $this->assertInstanceOf(FileSystem::class, fs(FileSystem::LOCAL));

        $this->assertInstanceOf(LocalFileSystemAdapter::class, fs(FileSystem::LOCAL)->getAdapter());
    }

    public function testFileSystemHelperGetDropboxFileSystem(): void
    {
        $this->assertInstanceOf(FileSystem::class, fs(FileSystem::DROPBOX));

        $this->assertInstanceOf(DropboxFileSystemAdapter::class, fs(FileSystem::DROPBOX)->getAdapter());
    }

    public function testFileSystemHelperGetGoogleDriveFileSystem(): void
    {
        $this->assertInstanceOf(FileSystem::class, fs(FileSystem::GDRIVE));

        $this->assertInstanceOf(GoogleDriveFileSystemAdapter::class, fs(FileSystem::GDRIVE)->getAdapter());
    }
}
