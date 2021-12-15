<?php

namespace Quantum\Test\Unit;

use Quantum\Libraries\Database\Sleekdb\SleekDbal;

require_once __DIR__ . DS . 'SleekDbalTestCase.php';

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