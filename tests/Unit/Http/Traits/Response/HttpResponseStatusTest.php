<?php

namespace Quantum\Tests\Unit\Http\Traits\Response;

use Quantum\Tests\Unit\AppTestCase;

class HttpResponseStatusTest extends AppTestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function testResponseStatus(): void
    {
        $response = response();

        $this->assertEquals(200, $response->getStatusCode());

        $returned = $response->setStatusCode(301);

        $this->assertSame($response, $returned);

        $this->assertEquals(301, $response->getStatusCode());

        $this->assertEquals('Moved Permanently', $response->getStatusText());
    }

    public function testHttpStatusGetText(): void
    {
        $response = response();

        $this->assertEquals('OK', $response->getText(200));

        $this->assertEquals('Not Found', $response->getText(404));

        $this->assertEquals('Internal Server Error', $response->getText(500));

        $this->expectException(\InvalidArgumentException::class);

        $response->getText(888);
    }
}
