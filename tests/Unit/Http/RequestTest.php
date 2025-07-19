<?php

namespace Quantum\Tests\Unit\Http;

use Quantum\Http\Request\HttpRequest;
use Quantum\Tests\Unit\AppTestCase;
use Quantum\Http\Request;

class RequestTest extends AppTestCase
{

    public function setUp(): void
    {
        parent::setUp();

    }

    public function tearDown(): void
    {
        HttpRequest::flush();
    }

    public function testSetGetMethod()
    {
        $request = new Request();

        $request->create('GET', '/');

        $this->assertEquals('GET', $request->getMethod());

        $request->setMethod('POST');

        $this->assertEquals('POST', $request->getMethod());
    }

    public function testIsMethod()
    {
        $request = new Request();

        $request->create('GET', '/');

        $this->assertTrue($request->isMethod('GET'));

        $this->assertTrue($request->isMethod('get'));

        $this->assertFalse($request->isMethod('POST'));

        $request->setMethod('POST');

        $this->assertTrue($request->isMethod('POST'));

        $this->assertTrue($request->isMethod('post'));
    }

    public function testGetCsrfToken()
    {
        $request = new Request();

        $this->assertNull($request->getCsrfToken());

        $request->create('PATCH', '/', ['csrf-token' => csrf_token()]);

        $this->assertNotNull($request->getCsrfToken());

    }
}
