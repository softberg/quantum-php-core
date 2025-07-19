<?php

namespace Http\Traits\Request;

use Quantum\Libraries\Storage\UploadedFile;
use Quantum\Tests\Unit\AppTestCase;
use Quantum\Environment\Server;
use Quantum\Http\Request;

class HttpRawInputTest extends AppTestCase
{

    public function setUp(): void
    {
        parent::setUp();
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

    public function testParseReturnsEmptyWhenNoBoundary()
    {
        Server::getInstance()->set('CONTENT_TYPE', null);

        $result = Request::parse('irrelevant-body');

        $this->assertEquals(['params' => [], 'files' => []], $result);
    }

    public function testParseWithParameterBlock()
    {
        $boundary = '----BoundaryTestParam';
        $rawInput = "--$boundary\r\n"
            . "Content-Disposition: form-data; name=\"username\"\r\n"
            . "\r\n"
            . "JohnDoe\r\n"
            . "--$boundary--\r\n";

        Server::getInstance()->set('CONTENT_TYPE', "multipart/form-data; boundary=$boundary");

        $result = Request::parse($rawInput);

        $this->assertArrayHasKey('params', $result);

        $this->assertEquals('JohnDoe', $result['params']['username']);
    }

    public function testParseWithStreamBlock()
    {
        $boundary = '----BoundaryStreamTest';

        $rawInput = "--$boundary\r\n"
            . "Content-Disposition: form-data; name=\"payload\"\r\n"
            . "Content-Type: application/octet-stream\r\n"
            . "\r\n"
            . "stream-data\r\n"
            . "--$boundary--\r\n";

        Server::getInstance()->set('CONTENT_TYPE', "multipart/form-data; boundary=$boundary");

        $result = Request::parse($rawInput);

        $this->assertArrayHasKey('params', $result);

        $this->assertEquals('stream-data', $result['params']['payload']);
    }

    public function testParseWithFileBlock()
    {
        $boundary = '----BoundaryFileTest';
        $fileContent = 'file-content';
        $filename = 'sample.txt';
        $name = 'upload';

        $rawInput = "--$boundary\r\n"
            . "Content-Disposition: form-data; name=\"$name\"; filename=\"$filename\"\r\n"
            . "Content-Type: text/plain\r\n"
            . "\r\n"
            . "$fileContent\r\n"
            . "--$boundary--\r\n";

        Server::getInstance()->set('CONTENT_TYPE', "multipart/form-data; boundary=$boundary");

        $result = Request::parse($rawInput);

        $this->assertArrayHasKey('files', $result);

        $this->assertArrayHasKey('upload', $result['files']);

        $this->assertInstanceOf(UploadedFile::class, $result['files']['upload']);

        $uploadedFile = $result['files']['upload'];

        $this->assertEquals($filename, $uploadedFile->getNameWithExtension());
    }
}
