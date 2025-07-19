<?php

namespace Http\Traits\Request;

use Quantum\Libraries\Storage\UploadedFile;
use Quantum\Http\Request\HttpRequest;
use Quantum\Tests\Unit\AppTestCase;
use Quantum\Environment\Server;
use Quantum\Http\Request;

class HttpRequestInternalTest extends AppTestCase
{

    public function setUp(): void
    {
        parent::setUp();
    }

    public function testCreateRequestSetsBasicServerParams()
    {
        Request::create('GET', 'https://example.com/test/path?foo=bar');

        $server = Server::getInstance();

        $this->assertEquals('GET', $server->get('REQUEST_METHOD'));
        $this->assertEquals('test/path', $server->get('REQUEST_URI'));
        $this->assertEquals('https', $server->get('REQUEST_SCHEME'));
        $this->assertEquals('on', $server->get('HTTPS'));
        $this->assertEquals('example.com', $server->get('HTTP_HOST'));
        $this->assertEquals('example.com', $server->get('SERVER_NAME'));
        $this->assertEquals(443, $server->get('SERVER_PORT'));
        $this->assertEquals('foo=bar', $server->get('QUERY_STRING'));
    }

    public function testContentTypeIsMultipartWhenFilesProvided()
    {
        $files = [
            'file' => [
                'name' => 'test.txt',
                'type' => 'text/plain',
                'tmp_name' => '/tmp/php123',
                'error' => 0,
                'size' => 100,
            ]
        ];

        Request::create('POST', 'http://localhost/upload', [], [], $files);

        $this->assertEquals('multipart/form-data', Server::getInstance()->get('CONTENT_TYPE'));
    }

    public function testContentTypeIsFormUrlencodedWhenDataProvided()
    {
        Request::create('POST', 'http://localhost/form', ['key' => 'value']);

        $this->assertEquals('application/x-www-form-urlencoded', Server::getInstance()->get('CONTENT_TYPE'));
    }

    public function testContentTypeIsTextHtmlWhenNoDataOrFiles()
    {
        Request::create('GET', 'http://localhost');

        $this->assertEquals('text/html', Server::getInstance()->get('CONTENT_TYPE'));
    }

    public function testRequestParamsAreSet()
    {
        $data = ['foo' => 'bar'];

        Request::create('POST', 'http://localhost/submit', $data);

        $this->assertEquals('bar', HttpRequest::get('foo'));
    }

    public function testUploadedFilesAreSet()
    {
        $files = [
            'document' => [
                'name' => 'doc.txt',
                'type' => 'text/plain',
                'tmp_name' => '/tmp/php456',
                'error' => 0,
                'size' => 456,
            ]
        ];

        $request = new Request();

        $request->create('POST', 'http://localhost/upload', [], [], $files);

        $this->assertTrue($request->hasFile('document'));

        $this->assertInstanceOf(UploadedFile::class, $request->getFile('document'));

        $this->assertEquals('doc.txt', $request->getFile('document')->getNameWithExtension());
    }
}