<?php

namespace Quantum\Tests\Unit\Database\Exceptions;

use Quantum\Database\Exceptions\DatabaseException;
use Quantum\Tests\Unit\AppTestCase;

class DatabaseExceptionTest extends AppTestCase
{
    public function testIncorrectConfig(): void
    {
        $exception = DatabaseException::incorrectConfig();

        $this->assertInstanceOf(DatabaseException::class, $exception);
        $this->assertSame('The structure of config is not correct', $exception->getMessage());
        $this->assertSame(E_ERROR, $exception->getCode());
    }

    public function testOperatorNotSupported(): void
    {
        $exception = DatabaseException::operatorNotSupported('foo');

        $this->assertInstanceOf(DatabaseException::class, $exception);
        $this->assertSame('The operator `foo` is not supported', $exception->getMessage());
        $this->assertSame(E_WARNING, $exception->getCode());
    }

    public function testTableAlreadyExists(): void
    {
        $exception = DatabaseException::tableAlreadyExists('users');

        $this->assertInstanceOf(DatabaseException::class, $exception);
        $this->assertSame('The table `users` is already exists', $exception->getMessage());
        $this->assertSame(E_ERROR, $exception->getCode());
    }

    public function testTableDoesNotExists(): void
    {
        $exception = DatabaseException::tableDoesNotExists('users');

        $this->assertInstanceOf(DatabaseException::class, $exception);
        $this->assertSame('The table `users` does not exists', $exception->getMessage());
        $this->assertSame(E_ERROR, $exception->getCode());
    }
}

