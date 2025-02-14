<?php

namespace Quantum\Tests\Unit\Libraries\Storage\Factories;

use Quantum\Libraries\Storage\Adapters\GoogleDrive\GoogleDriveFileSystemAdapter;
use Quantum\Libraries\Storage\Adapters\Dropbox\DropboxFileSystemAdapter;
use Quantum\Libraries\Storage\Adapters\Local\LocalFileSystemAdapter;
use Quantum\Libraries\Storage\Adapters\GoogleDrive\GoogleDriveApp;
use Quantum\Libraries\Storage\Exceptions\FileSystemException;
use Quantum\Libraries\Storage\Factories\FileSystemFactory;
use Quantum\Libraries\Storage\Adapters\Dropbox\DropboxApp;
use Quantum\Libraries\Storage\FileSystem;
use Quantum\Tests\Unit\AppTestCase;
use Mockery;

class FileSystemFactoryTest extends AppTestCase
{

    private static $response = [];

    public function setUp(): void
    {
        parent::setUp();
    }

    public function testFileSystemFactoryInstance()
    {
        $fs = FileSystemFactory::get();

        $this->assertInstanceOf(FileSystem::class, $fs);
    }

    public function testFileSystemFactoryLocalAdapter()
    {
        $fs = FileSystemFactory::get();

        $this->assertInstanceOf(LocalFileSystemAdapter::class, $fs->getAdapter());
    }

    public function testFileSystemFactoryDropboxAdapter()
    {
        $dropboxAppMock = Mockery::mock(DropboxApp::class);

        $dropboxAppMock
            ->shouldReceive('rpcRequest')
            ->andReturnUsing(function ($endpoint, $params) {
                self::$response = array_merge(self::$response, $params);
                return self::$response;
            });

        $dropboxAppMock
            ->shouldReceive('contentRequest')
            ->andReturnUsing(function ($endpoint, $params, $content = '') {
                if ($content) {
                    self::$response['content'] = $content;
                }

                return self::$response['content'] ?? false;
            });

        $dropboxAppMock
            ->shouldReceive('path')
            ->andReturnUsing(function ($path) {
                return ['path' => '/' . trim($path, '/')];
            });

        $fs = FileSystemFactory::get(FileSystem::DROPBOX, $dropboxAppMock);

        $this->assertInstanceOf(DropboxFileSystemAdapter::class, $fs->getAdapter());
    }

    public function testFileSystemFactoryGoogleDriveAdapter()
    {
        $googleDriveAppMock = Mockery::mock(GoogleDriveApp::class)->makePartial();

        $googleDriveAppMock->shouldReceive('rpcRequest')->andReturnUsing(function ($endpoint, $params) {
            if(str_contains($endpoint, '?alt=media')){
                return self::$response[array_key_last(self::$response)];
            }
            self::$response = array_merge(self::$response, (array)$params);
            return (object)self::$response;
        });

        $fs = FileSystemFactory::get(FileSystem::GDRIVE, $googleDriveAppMock);

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
        $fs1 = FileSystemFactory::get();
        $fs2 = FileSystemFactory::get();

        $this->assertSame($fs1, $fs2);
    }
}