<?php

namespace Quantum\Tests\Unit\Database\Adapters\Sleekdb;

use Quantum\Database\Adapters\Sleekdb\SleekDbal;

class SleekDbalTest extends SleekDbalTestCase
{
    public function testSleekConstructor(): void
    {
        $userModel = new SleekDbal('users');

        $this->assertInstanceOf(SleekDbal::class, $userModel);
    }

    public function testSleekGetTable(): void
    {
        $userModel = new SleekDbal('users');

        $this->assertEquals('users', $userModel->getTable());
    }
}
