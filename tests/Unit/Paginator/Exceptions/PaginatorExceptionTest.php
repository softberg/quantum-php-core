<?php

namespace Quantum\Tests\Unit\Paginator\Exceptions;

use Quantum\Paginator\Exceptions\PaginatorException;
use Quantum\Tests\Unit\AppTestCase;

class PaginatorExceptionTest extends AppTestCase
{
    public function testMissingRequiredParams(): void
    {
        $exception = PaginatorException::missingRequiredParams('array', 'items');

        $this->assertInstanceOf(PaginatorException::class, $exception);
        $this->assertSame('Missing required parameter `items` missing for adapter Array', $exception->getMessage());
        $this->assertSame(E_WARNING, $exception->getCode());
    }
}
