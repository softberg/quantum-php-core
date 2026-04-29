<?php

namespace Quantum\Tests\Unit\Storage\Exceptions;

use Quantum\Storage\Exceptions\FileSystemException;
use Quantum\Tests\Unit\AppTestCase;

class FileSystemExceptionTest extends AppTestCase
{
    public function testDirectoryNotExists(): void
    {
        $exception = FileSystemException::directoryNotExists('/tmp/a');
        $this->assertSame('The directory /tmp/a does not exists.', $exception->getMessage());
        $this->assertSame(E_WARNING, $exception->getCode());
    }

    public function testDirectoryNotWritable(): void
    {
        $exception = FileSystemException::directoryNotWritable('/tmp/b');
        $this->assertSame('The directory /tmp/b is not writable.', $exception->getMessage());
        $this->assertSame(E_WARNING, $exception->getCode());
    }

    public function testFileAlreadyExists(): void
    {
        $exception = FileSystemException::fileAlreadyExists('/tmp/c.txt');
        $this->assertSame('The file /tmp/c.txt already exists.', $exception->getMessage());
        $this->assertSame(E_WARNING, $exception->getCode());
    }
}

