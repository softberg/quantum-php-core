<?php

namespace Http\Traits\Request;

use Quantum\Tests\Unit\AppTestCase;
use Quantum\Http\Request;

class HttpRequestUrlTest extends AppTestCase
{

    public function setUp(): void
    {
        parent::setUp();
    }

    public function tearDown(): void
    {
        Request::flush();
    }

    public function testSetGetProtocol()
    {
        $request = new Request();

        $request->create('GET', 'https://test.com');

        $this->assertEquals('https', $request->getProtocol());

        $request->setProtocol('http');

        $this->assertEquals('http', $request->getProtocol());
    }

    public function testSetGetHost()
    {
        $request = new Request();

        $request->create('GET', 'https://test.com/dashboard');

        $this->assertEquals('test.com', $request->getHost());

        $request->setHost('tester.com');

        $this->assertEquals('tester.com', $request->getHost());
    }

    public function testSetGetPort()
    {
        $request = new Request();

        $request->create('GET', 'https://test.com:8080/dashboard');

        $this->assertEquals('8080', $request->getPort());

        $request->setPort('9000');

        $this->assertEquals('9000', $request->getPort());
    }

    public function testSetGetUri()
    {
        $request = new Request();

        $request->create('GET', 'http://test.com/post/12');

        $this->assertEquals('post/12', $request->getUri());

        $request->setUri('post/edit/12');

        $this->assertEquals('post/edit/12', $request->getUri());
    }


    public function testGetSegments()
    {
        $request = new Request();

        $request->create('GET', 'post/12/notes');

        $this->assertIsArray($request->getAllSegments());

        $this->assertEquals('post', $request->getSegment(1));

        $this->assertEquals('12', $request->getSegment(2));

        $this->assertNull($request->getSegment(10));
    }
}