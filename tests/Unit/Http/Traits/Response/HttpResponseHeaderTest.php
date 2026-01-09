<?php

namespace Http\Traits\Response;

use Quantum\Tests\Unit\AppTestCase;
use Quantum\Http\Response;

class HttpResponseHeaderTest extends AppTestCase
{
    public function setUp(): void
    {
        parent::setUp();

    }

    public function testResponseHeaderSetHasGetAllDelete()
    {
        $response = new Response();

        $this->assertEmpty($response->allHeaders());

        $this->assertFalse($response->hasHeader('X-Frame-Options'));

        $response->setHeader('X-Frame-Options', 'deny');

        $this->assertTrue($response->hasHeader('X-Frame-Options'));

        $this->assertEquals('deny', $response->getHeader('X-Frame-Options'));

        $this->assertIsArray($response->allHeaders());

        $response->deleteHeader('X-Frame-Options');

        $this->assertFalse($response->hasHeader('X-Frame-Options'));

        $this->assertNull($response->getHeader('X-Frame-Options'));
    }
}
