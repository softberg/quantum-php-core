<?php

namespace Quantum\Tests\Unit\App\Exceptions;

use Quantum\App\Exceptions\AppException;
use Quantum\Tests\Unit\AppTestCase;

class AppExceptionTest extends AppTestCase
{
    public function testMissingAppKeyFactory(): void
    {
        $exception = AppException::missingAppKey();

        $this->assertInstanceOf(AppException::class, $exception);
        $this->assertSame('APP KEY is missing.', $exception->getMessage());
        $this->assertSame(E_ERROR, $exception->getCode());
    }
}
