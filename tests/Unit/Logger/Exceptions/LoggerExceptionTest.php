<?php

namespace Quantum\Tests\Unit\Logger\Exceptions;

use Quantum\Logger\Exceptions\LoggerException;
use Quantum\Tests\Unit\AppTestCase;

class LoggerExceptionTest extends AppTestCase
{
    public function testLogPathIsNotDirectory(): void
    {
        $exception = LoggerException::logPathIsNotDirectory('/tmp/logs.txt');

        $this->assertInstanceOf(LoggerException::class, $exception);
        $this->assertSame('Log path is not point to a directory.', $exception->getMessage());
        $this->assertSame(E_ERROR, $exception->getCode());
    }

    public function testLogPathIsNotFile(): void
    {
        $exception = LoggerException::logPathIsNotFile('/tmp/logs');

        $this->assertInstanceOf(LoggerException::class, $exception);
        $this->assertSame('Log path is not point to a file.', $exception->getMessage());
        $this->assertSame(E_ERROR, $exception->getCode());
    }
}
