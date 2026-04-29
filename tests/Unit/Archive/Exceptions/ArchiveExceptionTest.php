<?php

namespace Quantum\Tests\Unit\Archive\Exceptions;

use Quantum\Archive\Exceptions\ArchiveException;
use Quantum\Tests\Unit\AppTestCase;

class ArchiveExceptionTest extends AppTestCase
{
    public function testCantOpen(): void
    {
        $exception = ArchiveException::cantOpen('demo.zip');

        $this->assertInstanceOf(ArchiveException::class, $exception);
        $this->assertSame('The archive `demo.zip` can not be opened', $exception->getMessage());
        $this->assertSame(E_WARNING, $exception->getCode());
    }

    public function testMissingArchiveName(): void
    {
        $exception = ArchiveException::missingArchiveName();

        $this->assertInstanceOf(ArchiveException::class, $exception);
        $this->assertSame('Archive name is not set', $exception->getMessage());
        $this->assertSame(E_WARNING, $exception->getCode());
    }
}
