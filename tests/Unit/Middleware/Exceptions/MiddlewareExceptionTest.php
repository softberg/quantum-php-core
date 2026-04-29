<?php

namespace Quantum\Tests\Unit\Middleware\Exceptions;

use Quantum\Middleware\Exceptions\MiddlewareException;
use Quantum\Tests\Unit\AppTestCase;

class MiddlewareExceptionTest extends AppTestCase
{
    public function testMiddlewareNotFound(): void
    {
        $exception = MiddlewareException::middlewareNotFound('App\\Middlewares\\Auth');

        $this->assertInstanceOf(MiddlewareException::class, $exception);
        $this->assertSame('Middleware class `App\\Middlewares\\Auth` not found.', $exception->getMessage());
        $this->assertSame(E_ERROR, $exception->getCode());
    }
}

