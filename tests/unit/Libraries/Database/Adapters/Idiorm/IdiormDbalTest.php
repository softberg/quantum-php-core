<?php

namespace Quantum\Tests\Libraries\Database\Adapters\Idiorm;

use Quantum\Libraries\Database\Adapters\Idiorm\IdiormDbal;

class IdiormDbalTest extends IdiormDbalTestCase
{
    public function testIdiormConstructor()
    {
        $userModel = new IdiormDbal('users');

        $this->assertInstanceOf(IdiormDbal::class, $userModel);
    }

    public function testIdiormGetTable()
    {
        $userModel = new IdiormDbal('users');

        $this->assertEquals('users', $userModel->getTable());
    }
}

