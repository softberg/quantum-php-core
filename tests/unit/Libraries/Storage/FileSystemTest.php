<?php

use Quantum\Libraries\Storage\Adapters\Dropbox\DropboxFileSystemAdapter;
use Quantum\Libraries\Storage\Adapters\Local\LocalFileSystemAdapter;
use Quantum\Libraries\Storage\Adapters\Dropbox\DropboxApp;
use Quantum\Libraries\Storage\FilesystemAdapterInterface;
use Quantum\Libraries\Storage\FileSystem;
use Quantum\Tests\AppTestCase;

class FileSystemTest extends AppTestCase
{

    private $dropboxAppMock;

    public function setUp(): void
    {
        parent::setUp();

        $this->filename = base_dir() . DS . 'test.txt';

        $this->dropboxAppMock = Mockery::mock(DropboxApp::class);
    }

    public function testFileSystemAdapter()
    {
        $localFs = new FileSystem();

        $this->assertInstanceOf(FilesystemAdapterInterface::class, $localFs->getAdapter());

        $this->assertInstanceOf(LocalFileSystemAdapter::class, $localFs->getAdapter());

        $dropboxFs = new FileSystem(DropboxFileSystemAdapter::getInstance($this->dropboxAppMock));

        $this->assertInstanceOf(DropboxFileSystemAdapter::class, $dropboxFs->getAdapter());
    }

}
