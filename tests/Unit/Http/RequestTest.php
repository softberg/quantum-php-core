<?php

namespace Quantum\Tests\Unit\Http;

use Quantum\Tests\Unit\AppTestCase;

class RequestTest extends AppTestCase
{
    public function setUp(): void
    {
        parent::setUp();

    }

    public function tearDown(): void
    {
        request()->flush();
    }

    public function testSetGetMethod(): void
    {
        $request = request();

        $request->create('GET', '/');

        $this->assertEquals('GET', $request->getMethod());

        $request->setMethod('POST');

        $this->assertEquals('POST', $request->getMethod());
    }

    public function testIsMethod(): void
    {
        $request = request();

        $request->create('GET', '/');

        $this->assertTrue($request->isMethod('GET'));

        $this->assertTrue($request->isMethod('get'));

        $this->assertFalse($request->isMethod('POST'));

        $request->setMethod('POST');

        $this->assertTrue($request->isMethod('POST'));

        $this->assertTrue($request->isMethod('post'));
    }

    public function testGetCsrfToken(): void
    {
        $request = request();

        $this->assertNull($request->getCsrfToken());

        $request->create('PATCH', '/', ['csrf-token' => csrf_token()]);

        $this->assertNotNull($request->getCsrfToken());

    }
}
