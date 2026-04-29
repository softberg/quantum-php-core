<?php

namespace Quantum\Tests\Unit\Csrf\Exceptions;

use Quantum\Csrf\Exceptions\CsrfException;
use Quantum\Tests\Unit\AppTestCase;

class CsrfExceptionTest extends AppTestCase
{
    public function testTokenNotFound(): void
    {
        $exception = CsrfException::tokenNotFound();

        $this->assertInstanceOf(CsrfException::class, $exception);
        $this->assertSame('CSRF Token is missing', $exception->getMessage());
        $this->assertSame(E_WARNING, $exception->getCode());
    }

    public function testTokenNotMatched(): void
    {
        $exception = CsrfException::tokenNotMatched();

        $this->assertInstanceOf(CsrfException::class, $exception);
        $this->assertSame('CSRF Token does not matched', $exception->getMessage());
        $this->assertSame(E_WARNING, $exception->getCode());
    }
}
