<?php

namespace Http\Traits\Request;

use Quantum\Libraries\Storage\Exceptions\FileUploadException;
use Quantum\Libraries\Storage\UploadedFile;
use Quantum\Tests\Unit\AppTestCase;
use Quantum\Http\Request;

class HttpRequestFileTest extends AppTestCase
{

    public function setUp(): void
    {
        parent::setUp();
    }

    public function tearDown(): void
    {
        Request::flush();
    }

    public function testHasGetFile()
    {
        $request = new Request();

        $file = [
            'image' => [
                'size' => 500,
                'name' => 'foo.jpg',
                'tmp_name' => '/tmp/php8fe2.tmp',
                'type' => 'image/jpg',
                'error' => 0,
            ],
        ];

        $request->create('POST', '/upload', [], [], $file);

        $this->assertTrue($request->hasFile('image'));

        $this->assertInstanceOf(UploadedFile::class, $request->getFile('image'));

        $fileWithError = [
            'image' => [
                'size' => 500,
                'name' => 'foo.jpg',
                'tmp_name' => '/tmp/php8fe2.tmp',
                'type' => 'image/jpg',
                'error' => 4,
            ],
        ];

        $request->create('POST', '/upload', [], [], $fileWithError);

        $this->assertFalse($request->hasFile('image'));

        $this->expectException(FileUploadException::class);

        $this->expectExceptionMessage('The file `image` not found');

        $request->getFile('image');
    }

    public function testGetMultipleFiles()
    {
        $request = new Request();

        $this->assertFalse($request->hasFile('image'));

        $files = [
            'image' => [
                'size' => [500, 800],
                'name' => ['foo.jpg', 'bar.png'],
                'tmp_name' => ['/tmp/php8fe2.tmp', '/tmp/php8fe3.tmp'],
                'type' => ['image/jpg', 'image/png'],
                'error' => [0, 0],
            ],
        ];

        $request->create('POST', '/upload', [], [], $files);

        $this->assertTrue($request->hasFile('image'));

        $image = $request->getFile('image');

        $this->assertIsArray($image);

        $this->assertInstanceOf(UploadedFile::class, $image[0]);

        $this->assertEquals('foo.jpg', $image[0]->getNameWithExtension());

        $this->assertEquals('bar.png', $image[1]->getNameWithExtension());
    }
}