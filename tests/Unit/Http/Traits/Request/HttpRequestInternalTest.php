<?php

namespace Quantum\Tests\Unit\Http\Traits\Request;

use Quantum\Tests\Unit\AppTestCase;
use Quantum\Storage\UploadedFile;
use Quantum\Http\Request;

class HttpRequestInternalTest extends AppTestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function testCreateRequestSetsBasicServerParams(): void
    {
        Request::create('GET', 'https://example.com/test/path?foo=bar');

        $server = server();

        $this->assertEquals('GET', $server->get('REQUEST_METHOD'));
        $this->assertEquals('test/path', $server->get('REQUEST_URI'));
        $this->assertEquals('https', $server->get('REQUEST_SCHEME'));
        $this->assertEquals('on', $server->get('HTTPS'));
        $this->assertEquals('example.com', $server->get('HTTP_HOST'));
        $this->assertEquals('example.com', $server->get('SERVER_NAME'));
        $this->assertEquals(443, $server->get('SERVER_PORT'));
        $this->assertEquals('foo=bar', $server->get('QUERY_STRING'));
    }

    public function testContentTypeIsMultipartWhenFilesProvided(): void
    {
        $files = [
            'file' => [
                'name' => 'test.txt',
                'type' => 'text/plain',
                'tmp_name' => '/tmp/php123',
                'error' => 0,
                'size' => 100,
            ],
        ];

        Request::create('POST', 'http://localhost/upload', [], [], $files);

        $this->assertEquals('multipart/form-data', server()->get('CONTENT_TYPE'));
    }

    public function testContentTypeIsFormUrlencodedWhenDataProvided(): void
    {
        Request::create('POST', 'http://localhost/form', ['key' => 'value']);

        $this->assertEquals('application/x-www-form-urlencoded', server()->get('CONTENT_TYPE'));
    }

    public function testContentTypeIsTextHtmlWhenNoDataOrFiles(): void
    {
        Request::create('GET', 'http://localhost');

        $this->assertEquals('text/html', server()->get('CONTENT_TYPE'));
    }

    public function testRequestParamsAreSet(): void
    {
        $data = ['foo' => 'bar'];

        Request::create('POST', 'http://localhost/submit', $data);

        $this->assertEquals('bar', Request::get('foo'));
    }

    public function testUploadedFilesAreSet(): void
    {
        $files = [
            'document' => [
                'name' => 'doc.txt',
                'type' => 'text/plain',
                'tmp_name' => '/tmp/php456',
                'error' => 0,
                'size' => 456,
            ],
        ];

        $request = new Request();

        $request->create('POST', 'http://localhost/upload', [], [], $files);

        $this->assertTrue($request->hasFile('document'));

        $this->assertInstanceOf(UploadedFile::class, $request->getFile('document'));

        $this->assertEquals('doc.txt', $request->getFile('document')->getNameWithExtension());
    }
}
