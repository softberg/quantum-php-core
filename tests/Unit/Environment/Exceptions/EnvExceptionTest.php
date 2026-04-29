<?php

namespace Quantum\Tests\Unit\Environment\Exceptions;

use Quantum\Environment\Exceptions\EnvException;
use Quantum\Tests\Unit\AppTestCase;

class EnvExceptionTest extends AppTestCase
{
    public function testEnvironmentImmutable(): void
    {
        $exception = EnvException::environmentImmutable();

        $this->assertInstanceOf(EnvException::class, $exception);
        $this->assertSame('The environment is immutable. Modifications are not allowed.', $exception->getMessage());
        $this->assertSame(E_ERROR, $exception->getCode());
    }

    public function testEnvironmentNotLoaded(): void
    {
        $exception = EnvException::environmentNotLoaded();

        $this->assertInstanceOf(EnvException::class, $exception);
        $this->assertSame('Environment not loaded. Call `load()` method first.', $exception->getMessage());
        $this->assertSame(E_ERROR, $exception->getCode());
    }
}
