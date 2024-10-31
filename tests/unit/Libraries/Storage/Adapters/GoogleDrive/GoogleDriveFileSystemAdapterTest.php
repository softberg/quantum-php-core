<?php

namespace Quantum\Tests\Libraries\Storage\Adapters\GoogleDrive;

use Quantum\Libraries\Storage\Adapters\GoogleDrive\GoogleDriveFileSystemAdapter;
use Quantum\Libraries\Storage\Adapters\GoogleDrive\GoogleDriveApp;
use Quantum\Tests\AppTestCase;
use Mockery;

class GoogleDriveFileSystemAdapterTest extends AppTestCase
{
    private $fs;
    private $dirname = 'common';
    private $filename = 'sample.txt';
    private $content = 'This file was created via dropbox api';
    private static $response = [];

    public function setUp(): void
    {
        parent::setUp();

        $googleDrive = Mockery::mock(GoogleDriveApp::class)->makePartial();

        $googleDrive->shouldReceive('rpcRequest')->andReturnUsing(function ($endpoint, $params) {
            if(str_contains($endpoint, '?alt=media')){
                return self::$response[array_key_last(self::$response)];
            }
            self::$response = array_merge(self::$response, (array)$params);
            return (object)self::$response;
        });

        $this->fs = GoogleDriveFileSystemAdapter::getInstance($googleDrive);
    }

    public function tearDown(): void
    {
        self::$response = [];
    }

    public function testGoogleDriveMakeCheckRemoveDirectory()
    {
        $this->assertFalse($this->fs->isDirectory($this->dirname));

        self::$response['kind'] = 'drive#file';

        $this->fs->makeDirectory($this->dirname);

        $this->assertTrue($this->fs->isDirectory($this->dirname));

        $this->fs->removeDirectory($this->dirname);

        self::$response = [];

        $this->assertFalse($this->fs->isDirectory($this->dirname));
    }

    public function testGoogleDriveCreateGetCheckRemoveFile()
    {
        $this->assertFalse($this->fs->isFile($this->filename));

        self::$response['kind'] = 'drive#file';
        self::$response['mimeType'] = 'text/plain';

        $this->fs->put($this->filename, $this->content);

        $this->assertTrue($this->fs->isFile($this->filename));

        $this->assertTrue($this->fs->exists($this->filename));

        $this->assertEquals($this->content, $this->fs->get($this->filename));

        $this->fs->remove($this->filename);

        self::$response = [];

        $this->assertFalse($this->fs->exists($this->filename));
    }

    public function testGoogleDriveFileAppend()
    {
        self::$response['kind'] = 'drive#file';
        self::$response['mimeType'] = 'text/plain';

        $this->fs->put($this->filename, $this->content);

        $this->assertTrue($this->fs->exists($this->filename));

        $moreContent = 'The sun is shining';

        $this->fs->append($this->filename, $moreContent);

        $this->assertEquals($this->content . $moreContent, $this->fs->get($this->filename));
    }

    public function testGoogleDriveFileRename()
    {
        $this->fs->put($this->filename, $this->content);

        $newFilename = 'new_name.txt';

        $this->assertFalse($this->fs->exists($newFilename));

        self::$response['kind'] = 'drive#file';
        self::$response['mimeType'] = 'text/plain';

        $this->fs->rename($this->filename, $newFilename);

        $this->assertTrue($this->fs->exists($newFilename));

        $this->fs->remove($newFilename);
    }

    public function testGoogleDriveFileCopy()
    {
        $dirName = 'testing';

        $this->fs->makeDirectory($dirName);

        $this->fs->put($this->filename, $this->content);

        $this->assertFalse($this->fs->exists($dirName . '/' . $this->filename));

        self::$response['kind'] = 'drive#file';
        self::$response['mimeType'] = 'text/plain';

        $this->fs->copy($this->filename, $dirName . '/' . $this->filename);

        $this->assertTrue($this->fs->exists($dirName . '/' . $this->filename));

        $this->fs->remove($dirName . '/' . $this->filename);

        $this->fs->removeDirectory($dirName);
    }

    public function testGoogleDriveFileSize()
    {
        $text = 'some bytes';

        $this->fs->put($this->filename, $text);

        self::$response['size'] = strlen($text);

        $this->assertEquals(10, $this->fs->size($this->filename));
    }

    public function testGoogleDriveFileLastModified()
    {
        $modified = '2023-02-12T15:50:38Z';

        $this->fs->put($this->filename, $this->content);

        self::$response['modifiedTime'] = $modified;

        $this->assertIsInt($this->fs->lastModified($this->filename));

        $this->assertEquals(strtotime($modified), $this->fs->lastModified($this->filename));
    }

    public function testGoogleDriveListDirectory()
    {
        self::$response['files'] = [
            [
                "kind" => "drive#file",
                "mimeType" => 'application/vnd.google-apps.folder',
                "name" => "empty",
                "id" => "SziOaBdnr3oAAAAAAAAAWQ",
            ],
            [
                "kind" => "drive#file",
                "mimeType" => 'image/png',
                "name" => "logo.png",
                "id" => "SziOaBdnr3oAAAAAAAAAVQ",
                "modifiedTime" => "2023-02-24T15:34:44Z",
                "size" => 3455,
            ],
            [
                "kind" => "drive#file",
                "mimeType" => 'image/jpeg',
                "name" => "Image 19.jpg",
                "id" => "SziOaBdnr3oAAAAAAAAAVw",
                "modifiedTime" => "2023-03-01T17:12:58Z",
                "size" => 2083432,
            ]
        ];

        $entries = $this->fs->listDirectory('test');

        $this->assertIsArray($entries);

        $this->assertIsArray(current($entries));

        self::$response = [];

        $this->assertFalse($this->fs->listDirectory('test'));

    }

}

