<?php

namespace Quantum\Tests\Unit\Database\Helpers;

use Quantum\Tests\Unit\AppTestCase;
use Quantum\Database\Database;

class DbHelperTest extends AppTestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

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
}
