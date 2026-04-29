<?php

namespace Quantum\Tests\Unit\Storage\Exceptions;

use Quantum\Storage\Exceptions\FileUploadException;
use Quantum\Tests\Unit\AppTestCase;

class FileUploadExceptionTest extends AppTestCase
{
    public function testFileTypeNotAllowed(): void
    {
        $exception = FileUploadException::fileTypeNotAllowed('exe');
        $this->assertSame('The file type `exe` is not allowed.', $exception->getMessage());
        $this->assertSame(E_WARNING, $exception->getCode());
    }

    public function testIncorrectMimeTypesConfig(): void
    {
        $exception = FileUploadException::incorrectMimeTypesConfig('uploads');
        $this->assertSame('Could not load config `uploads` properly.', $exception->getMessage());
        $this->assertSame(E_ERROR, $exception->getCode());
    }
}

