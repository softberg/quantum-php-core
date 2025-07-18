<?php

namespace Http\Traits\Response;

use Quantum\Tests\Unit\AppTestCase;
use Quantum\Http\Response;

class HttpResponseStatusTest extends AppTestCase
{

    public function setUp(): void
    {
        parent::setUp();
    }

    public function testResponseStatus()
    {
        $response = new Response();

        $this->assertEquals(200, $response->getStatusCode());

        $response->setStatusCode(301);

        $this->assertEquals(301, $response->getStatusCode());

        $this->assertEquals('Moved Permanently', $response->getStatusText());
    }

    public function testHttpStatusGetText()
    {
        $response = new Response();

        $this->assertEquals('OK', $response->getText(200));

        $this->assertEquals('Not Found', $response->getText(404));

        $this->assertEquals('Internal Server Error', $response->getText(500));

        $this->expectException(\InvalidArgumentException::class);

        $response->getText(888);
    }
}