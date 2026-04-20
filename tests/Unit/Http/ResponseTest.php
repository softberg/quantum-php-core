<?php

namespace Quantum\Tests\Unit\Http;

use Quantum\Http\Enums\ContentType;
use Quantum\Tests\Unit\AppTestCase;
use Throwable;

class ResponseTest extends AppTestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function tearDown(): void
    {
        response()->flush();
    }

    public function testResponseContentType(): void
    {
        $response = response();

        $this->assertEquals(ContentType::HTML, $response->getContentType());

        $response->setContentType(ContentType::JSON);

        $this->assertEquals(ContentType::JSON, $response->getContentType());
    }

    public function testResponseRedirect(): void
    {
        $response = response();

        $this->assertFalse($response->hasHeader('Location'));

        try {
            $response->redirect('/');
        } catch (Throwable $e) {

        }

        $this->assertTrue($response->hasHeader('Location'));

        $this->assertEquals('/', $response->getHeader('Location'));

        $this->assertEquals(302, $response->getStatusCode());

        try {
            $response->redirect('/home', 301);
        } catch (Throwable $e) {

        }

        $this->assertEquals('/home', $response->getHeader('Location'));

        $this->assertEquals(301, $response->getStatusCode());
    }

}
