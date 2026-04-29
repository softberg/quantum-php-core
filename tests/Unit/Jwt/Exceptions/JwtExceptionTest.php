<?php

namespace Quantum\Tests\Unit\Jwt\Exceptions;

use Quantum\Jwt\Exceptions\JwtException;
use Quantum\Tests\Unit\AppTestCase;

class JwtExceptionTest extends AppTestCase
{
    public function testPayloadNotFound(): void
    {
        $exception = JwtException::payloadNotFound();

        $this->assertInstanceOf(JwtException::class, $exception);
        $this->assertSame('JWT payload is missing.', $exception->getMessage());
        $this->assertSame(E_WARNING, $exception->getCode());
    }
}

