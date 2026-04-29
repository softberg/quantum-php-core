<?php

namespace Quantum\Tests\Unit\HttpClient\Exceptions;

use Quantum\HttpClient\Exceptions\HttpClientException;
use Quantum\Tests\Unit\AppTestCase;

class HttpClientExceptionTest extends AppTestCase
{
    public function testRequestNotCreated(): void
    {
        $exception = HttpClientException::requestNotCreated();

        $this->assertInstanceOf(HttpClientException::class, $exception);
        $this->assertSame('Request is not created.', $exception->getMessage());
        $this->assertSame(E_WARNING, $exception->getCode());
    }
}

