<?php

namespace Quantum\Tests\Unit\Migration\Exceptions;

use Quantum\Migration\Exceptions\MigrationException;
use Quantum\Tests\Unit\AppTestCase;

class MigrationExceptionTest extends AppTestCase
{
    public function testWrongDirection(): void
    {
        $exception = MigrationException::wrongDirection();

        $this->assertInstanceOf(MigrationException::class, $exception);
        $this->assertSame('Migration direction can only be [up] or [down]', $exception->getMessage());
        $this->assertSame(E_ERROR, $exception->getCode());
    }

    public function testUnsupportedAction(): void
    {
        $exception = MigrationException::unsupportedAction('sync');

        $this->assertInstanceOf(MigrationException::class, $exception);
        $this->assertSame('The action `sync`, is not supported', $exception->getMessage());
        $this->assertSame(E_ERROR, $exception->getCode());
    }

    public function testNothingToMigrate(): void
    {
        $exception = MigrationException::nothingToMigrate();

        $this->assertInstanceOf(MigrationException::class, $exception);
        $this->assertSame('Nothing to migrate', $exception->getMessage());
        $this->assertSame(E_NOTICE, $exception->getCode());
    }

    public function testInvalidMigrationClass(): void
    {
        $exception = MigrationException::invalidMigrationClass('FooMigration');

        $this->assertInstanceOf(MigrationException::class, $exception);
        $this->assertSame('Migration class `FooMigration` must extend QtMigration', $exception->getMessage());
        $this->assertSame(E_ERROR, $exception->getCode());
    }
}
