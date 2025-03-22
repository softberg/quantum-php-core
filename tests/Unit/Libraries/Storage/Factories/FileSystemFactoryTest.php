<?php

namespace Quantum\Tests\Unit\Libraries\Storage\Factories;

use Quantum\Libraries\Storage\Adapters\GoogleDrive\GoogleDriveFileSystemAdapter;
use Quantum\Libraries\Storage\Adapters\Dropbox\DropboxFileSystemAdapter;
use Quantum\Libraries\Storage\Adapters\Local\LocalFileSystemAdapter;
use Quantum\Libraries\Storage\Exceptions\FileSystemException;
use Quantum\Libraries\Storage\Factories\FileSystemFactory;
use Quantum\Libraries\Storage\FileSystem;
use Quantum\Tests\Unit\AppTestCase;
use Mockery;

class FileSystemFactoryTest extends AppTestCase
{

    public function setUp(): void
    {
        parent::setUp();
    }

    public function testFileSystemFactoryInstance()
    {
        $fs = FileSystemFactory::get();

        $this->assertInstanceOf(FileSystem::class, $fs);
    }

    public function testFileSystemFactoryDefaultAdapter()
    {
        $fs = FileSystemFactory::get();

        $this->assertInstanceOf(LocalFileSystemAdapter::class, $fs->getAdapter());
    }

    public function testFileSystemFactoryLocalAdapter()
    {
        $fs = FileSystemFactory::get(FileSystem::LOCAL);

        $this->assertInstanceOf(LocalFileSystemAdapter::class, $fs->getAdapter());
    }

    public function testFileSystemFactoryDropboxAdapter()
    {
        $fs = FileSystemFactory::get(FileSystem::DROPBOX);

        $this->assertInstanceOf(DropboxFileSystemAdapter::class, $fs->getAdapter());
    }

    public function testFileSystemFactoryGoogleDriveAdapter()
    {
        $fs = FileSystemFactory::get(FileSystem::GDRIVE);

        $this->assertInstanceOf(GoogleDriveFileSystemAdapter::class, $fs->getAdapter());
    }

    public function testFileSystemFactoryInvalidTypeAdapter()
    {
        $this->expectException(FileSystemException::class);

        $this->expectExceptionMessage('The adapter `invalid_type` is not supported`');

        FileSystemFactory::get('invalid_type');
    }

    public function testFileSystemFactoryReturnsSameInstance()
    {
        $fs1 = FileSystemFactory::get(FileSystem::LOCAL);
        $fs2 = FileSystemFactory::get(FileSystem::LOCAL);

        $this->assertSame($fs1, $fs2);
    }
}