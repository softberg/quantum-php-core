<?php

namespace Quantum\Tests\Unit\Libraries\Storage\Adapters\GoogleDrive;

use Quantum\Libraries\Storage\Adapters\GoogleDrive\GoogleDriveFileSystemAdapter;
use Quantum\Libraries\Storage\Adapters\GoogleDrive\GoogleDriveApp;
use Quantum\Tests\Unit\AppTestCase;
use Mockery;

class GoogleDriveFileSystemAdapterTest extends AppTestCase
{

    /**
     * @var GoogleDriveFileSystemAdapter
     */
    protected $fs;

    /**
     * @var string
     */
    private $dirname = 'common';

    /**
     * @var string
     */
    private $filename = 'sample.txt';

    /**
     * @var string
     */
    private $newFilename = 'new_name.txt';

    /**
     * @var string
     */
    private $content = 'This file was created via gdrive api';

    /**
     * @var array
     */
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

        $this->fs = new GoogleDriveFileSystemAdapter($googleDrive);
    }

    public function tearDown(): void
    {
        self::$response = [];
    }

    public function testGoogleDriveMakeCheckRemoveDirectory()
    {
        $this->assertFalse($this->fs->isDirectory($this->dirname));

        self::$response['kind'] = GoogleDriveApp::DRIVE_FILE_KIND;

        $this->fs->makeDirectory($this->dirname);

        $this->assertTrue($this->fs->isDirectory($this->dirname));

        $this->fs->removeDirectory($this->dirname);

        self::$response = [];

        $this->assertFalse($this->fs->isDirectory($this->dirname));
    }

    public function testGoogleDriveCreateGetCheckRemoveFile()
    {
        $this->assertFalse($this->fs->isFile($this->filename));

        self::$response['kind'] = GoogleDriveApp::DRIVE_FILE_KIND;
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
        self::$response['kind'] = GoogleDriveApp::DRIVE_FILE_KIND;
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

        $this->assertFalse($this->fs->exists($this->newFilename));

        self::$response['kind'] = GoogleDriveApp::DRIVE_FILE_KIND;
        self::$response['mimeType'] = 'text/plain';

        $this->fs->rename($this->filename, $this->newFilename);

        $this->assertTrue($this->fs->exists($this->newFilename));

        $this->fs->remove($this->newFilename);
    }

    public function testGoogleDriveFileCopy()
    {
        $this->fs->makeDirectory($this->dirname);

        $this->fs->put($this->filename, $this->content);

        $this->assertFalse($this->fs->exists($this->dirname . '/' . $this->filename));

        self::$response['kind'] = GoogleDriveApp::DRIVE_FILE_KIND;
        self::$response['mimeType'] = 'text/plain';

        $this->fs->copy($this->filename, $this->dirname . '/' . $this->filename);

        $this->assertTrue($this->fs->exists($this->dirname . '/' . $this->filename));

        $this->fs->remove($this->dirname . '/' . $this->filename);

        $this->fs->removeDirectory($this->dirname);
    }

    public function testGoogleDriveFileSize()
    {
        $text = 'some bytes';

        $this->fs->put($this->filename, $text);

        self::$response['size'] = strlen($text);

        $this->assertEquals(strlen($text), $this->fs->size($this->filename));
    }

    public function testGoogleDriveFileLastModified()
    {
        $modified = '2023-02-12T15:50:38Z';

        $this->fs->put($this->filename, $this->content);

        self::$response['modifiedTime'] = $modified;

        $result = $this->fs->lastModified($this->filename);

        $this->assertIsInt($result);
        $this->assertEquals(strtotime($modified), $result);
    }

    public function testGoogleDriveListDirectory()
    {
        self::$response['files'] = [
            [
                "kind" => GoogleDriveApp::DRIVE_FILE_KIND,
                "mimeType" => 'application/vnd.google-apps.folder',
                "name" => "empty",
                "id" => "SziOaBdnr3oAAAAAAAAAWQ",
            ],
            [
                "kind" => GoogleDriveApp::DRIVE_FILE_KIND,
                "mimeType" => 'image/png',
                "name" => "logo.png",
                "id" => "SziOaBdnr3oAAAAAAAAAVQ",
                "modifiedTime" => "2023-02-24T15:34:44Z",
                "size" => 3455,
            ],
            [
                "kind" => GoogleDriveApp::DRIVE_FILE_KIND,
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

        $this->assertNull($this->fs->listDirectory('test'));

    }
}