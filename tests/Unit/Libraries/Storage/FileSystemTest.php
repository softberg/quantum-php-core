<?php

namespace Quantum\Tests\Unit\Libraries\Storage;

use Quantum\Libraries\Storage\Adapters\GoogleDrive\GoogleDriveFileSystemAdapter;
use Quantum\Libraries\Storage\Adapters\Dropbox\DropboxFileSystemAdapter;
use Quantum\Libraries\Storage\Adapters\Local\LocalFileSystemAdapter;
use Quantum\Libraries\Storage\Contracts\FilesystemAdapterInterface;
use Quantum\Libraries\Storage\Adapters\GoogleDrive\GoogleDriveApp;
use Quantum\Libraries\Storage\Exceptions\FileSystemException;
use Quantum\Libraries\Storage\Adapters\Dropbox\DropboxApp;
use Quantum\Libraries\Storage\FileSystem;
use Quantum\Tests\Unit\AppTestCase;
use Mockery;

class FileSystemTest extends AppTestCase
{

    public $googleDriveAppMock;
    private $dropboxAppMock;

    public function setUp(): void
    {
        parent::setUp();

        $this->dropboxAppMock = Mockery::mock(DropboxApp::class);

        $this->googleDriveAppMock = Mockery::mock(GoogleDriveApp::class);
    }

    public function testFileSystemGetAdapter()
    {
        $localFs = new FileSystem(new LocalFileSystemAdapter());

        $this->assertInstanceOf(LocalFileSystemAdapter::class, $localFs->getAdapter());

        $this->assertInstanceOf(FilesystemAdapterInterface::class, $localFs->getAdapter());


        $dropboxFs = new FileSystem(new DropboxFileSystemAdapter($this->dropboxAppMock));

        $this->assertInstanceOf(DropboxFileSystemAdapter::class, $dropboxFs->getAdapter());

        $this->assertInstanceOf(FilesystemAdapterInterface::class, $dropboxFs->getAdapter());


        $googleDriveFs = new FileSystem(new GoogleDriveFileSystemAdapter($this->googleDriveAppMock));

        $this->assertInstanceOf(GoogleDriveFileSystemAdapter::class, $googleDriveFs->getAdapter());

        $this->assertInstanceOf(FilesystemAdapterInterface::class, $googleDriveFs->getAdapter());
    }

    public function testFileSystemCallingValidMethod()
    {
        $fs = new FileSystem(new LocalFileSystemAdapter());

        $this->assertFalse($fs->exists('test.txt'));
    }

    public function testMailerCallingInvalidMethod()
    {
        $mailer = new FileSystem(new LocalFileSystemAdapter());

        $this->expectException(FileSystemException::class);

        $this->expectExceptionMessage('The method `callingInvalidMethod` is not supported for `' . LocalFileSystemAdapter::class . '`');

        $mailer->callingInvalidMethod();
    }
}