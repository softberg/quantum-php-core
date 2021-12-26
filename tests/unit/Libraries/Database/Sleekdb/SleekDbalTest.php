<?php

namespace Quantum\Tests\Libraries\Database\Sleekdb;

use Quantum\Libraries\Database\Sleekdb\SleekDbal;


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