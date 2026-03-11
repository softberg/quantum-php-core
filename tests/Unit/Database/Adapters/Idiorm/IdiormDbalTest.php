<?php

namespace Quantum\Tests\Unit\Database\Adapters\Idiorm;

use Quantum\Database\Adapters\Idiorm\IdiormDbal;

class IdiormDbalTest extends IdiormDbalTestCase
{
    public function testIdiormConstructor(): void
    {
        $userModel = new IdiormDbal('users');

        $this->assertInstanceOf(IdiormDbal::class, $userModel);
    }

    public function testIdiormGetTable(): void
    {
        $userModel = new IdiormDbal('users');

        $this->assertEquals('users', $userModel->getTable());
    }
}
