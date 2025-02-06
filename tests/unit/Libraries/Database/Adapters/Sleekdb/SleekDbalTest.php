<?php

namespace Quantum\Tests\Unit\Libraries\Database\Adapters\Sleekdb;

use Quantum\Libraries\Database\Adapters\Sleekdb\SleekDbal;

class SleekDbalTest extends SleekDbalTestCase
{

    public function testSleekConstructor()
    {
        $userModel = new SleekDbal('users');

        $this->assertInstanceOf(SleekDbal::class, $userModel);
    }

    public function testSleekGetTable()
    {
        $userModel = new SleekDbal('users');

        $this->assertEquals('users', $userModel->getTable());
    }
}