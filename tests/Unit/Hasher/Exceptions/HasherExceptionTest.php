<?php

namespace Quantum\Tests\Unit\Hasher\Exceptions;

use Quantum\Hasher\Exceptions\HasherException;
use Quantum\Tests\Unit\AppTestCase;

class HasherExceptionTest extends AppTestCase
{
    public function testAlgorithmNotSupported(): void
    {
        $exception = HasherException::algorithmNotSupported('foo');

        $this->assertSame('The algorithm foo not supported.', $exception->getMessage());
        $this->assertSame(E_WARNING, $exception->getCode());
    }

    public function testInvalidBcryptCost(): void
    {
        $exception = HasherException::invalidBcryptCost();

        $this->assertSame('Provided bcrypt cost is invalid.', $exception->getMessage());
        $this->assertSame(E_WARNING, $exception->getCode());
    }
}
