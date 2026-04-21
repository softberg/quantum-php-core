<?php

namespace Quantum\Tests\Unit\Database\Helpers;

use Quantum\Tests\Unit\AppTestCase;
use Quantum\Database\Database;
use Quantum\Di\Di;

class DbHelperTest extends AppTestCase
{
    public function testDbHelperReturnsDatabaseInstance(): void
    {
        $this->assertInstanceOf(Database::class, db());
    }

    public function testDbHelperReturnsSameInstance(): void
    {
        $first = db();
        $second = db();

        $this->assertSame($first, $second);
    }

    public function testDbHelperLazilyRegistersDatabase(): void
    {
        Di::resetContainer();

        $this->assertFalse(Di::isRegistered(Database::class));
        $this->assertInstanceOf(Database::class, db());
        $this->assertTrue(Di::isRegistered(Database::class));
    }
}
