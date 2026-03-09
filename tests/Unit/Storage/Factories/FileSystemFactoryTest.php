<?php

namespace Quantum\Tests\Unit\Storage\Factories;

use Quantum\Storage\Adapters\GoogleDrive\GoogleDriveFileSystemAdapter;
use Quantum\Storage\Adapters\Dropbox\DropboxFileSystemAdapter;
use Quantum\Storage\Adapters\Local\LocalFileSystemAdapter;
use Quantum\Storage\Exceptions\FileSystemException;
use Quantum\Storage\Factories\FileSystemFactory;
use Quantum\Tests\Unit\AppTestCase;
use Quantum\Storage\FileSystem;

class FileSystemFactoryTest extends AppTestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function testFileSystemFactoryInstance(): void
    {
        $fs = FileSystemFactory::get();

        $this->assertInstanceOf(FileSystem::class, $fs);
    }

    public function testFileSystemFactoryDefaultAdapter(): void
    {
        $fs = FileSystemFactory::get();

        $this->assertInstanceOf(LocalFileSystemAdapter::class, $fs->getAdapter());
    }

    public function testFileSystemFactoryLocalAdapter(): void
    {
        $fs = FileSystemFactory::get(FileSystem::LOCAL);

        $this->assertInstanceOf(LocalFileSystemAdapter::class, $fs->getAdapter());
    }

    public function testFileSystemFactoryDropboxAdapter(): void
    {
        $fs = FileSystemFactory::get(FileSystem::DROPBOX);

        $this->assertInstanceOf(DropboxFileSystemAdapter::class, $fs->getAdapter());
    }

    public function testFileSystemFactoryGoogleDriveAdapter(): void
    {
        $fs = FileSystemFactory::get(FileSystem::GDRIVE);

        $this->assertInstanceOf(GoogleDriveFileSystemAdapter::class, $fs->getAdapter());
    }

    public function testFileSystemFactoryInvalidTypeAdapter(): void
    {
        $this->expectException(FileSystemException::class);

        $this->expectExceptionMessage('The adapter `invalid_type` is not supported');

        FileSystemFactory::get('invalid_type');
    }

    public function testFileSystemFactoryReturnsSameInstance(): void
    {
        $fs1 = FileSystemFactory::get(FileSystem::LOCAL);
        $fs2 = FileSystemFactory::get(FileSystem::LOCAL);

        $this->assertSame($fs1, $fs2);
    }
}
