<?php

namespace Libraries\Storage\Helpers;

use Quantum\Libraries\Storage\Adapters\GoogleDrive\GoogleDriveFileSystemAdapter;
use Quantum\Libraries\Storage\Adapters\Dropbox\DropboxFileSystemAdapter;
use Quantum\Libraries\Storage\Adapters\Local\LocalFileSystemAdapter;
use Quantum\Libraries\Storage\FileSystem;
use Quantum\Tests\Unit\AppTestCase;

class FileSystemHelperFunctionsTest extends AppTestCase
{
    public function testFileSystemHelperGetDefaultFileSystem()
    {
        $this->assertInstanceOf(FileSystem::class, fs());

        $this->assertInstanceOf(LocalFileSystemAdapter::class, fs()->getAdapter());
    }

    public function testFileSystemHelperGetLocalFileSystem()
    {
        $this->assertInstanceOf(FileSystem::class, fs(FileSystem::LOCAL));

        $this->assertInstanceOf(LocalFileSystemAdapter::class, fs(FileSystem::LOCAL)->getAdapter());
    }

    public function testFileSystemHelperGetDropboxFileSystem()
    {
        $this->assertInstanceOf(FileSystem::class, fs(FileSystem::DROPBOX));

        $this->assertInstanceOf(DropboxFileSystemAdapter::class, fs(FileSystem::DROPBOX)->getAdapter());
    }

    public function testFileSystemHelperGetGoogleDriveFileSystem()
    {
        $this->assertInstanceOf(FileSystem::class, fs(FileSystem::GDRIVE));

        $this->assertInstanceOf(GoogleDriveFileSystemAdapter::class, fs(FileSystem::GDRIVE)->getAdapter());
    }
}
