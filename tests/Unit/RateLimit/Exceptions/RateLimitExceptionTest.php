<?php

namespace Quantum\Tests\Unit\RateLimit\Exceptions;

use Quantum\RateLimit\Exceptions\RateLimitException;
use Quantum\Tests\Unit\AppTestCase;

class RateLimitExceptionTest extends AppTestCase
{
    public function testRateLimitExceptionAdapterNotSupported(): void
    {
        $exception = RateLimitException::adapterNotSupported('invalid');

        $this->assertSame(
            'Rate limit adapter `invalid` is not supported.',
            $exception->getMessage()
        );
        $this->assertSame(E_ERROR, $exception->getCode());
    }
}
