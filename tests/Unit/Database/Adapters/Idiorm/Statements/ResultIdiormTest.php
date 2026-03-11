<?php

namespace Quantum\Tests\Unit\Database\Adapters\Idiorm\Statements;

use Quantum\Tests\Unit\Database\Adapters\Idiorm\IdiormDbalTestCase;
use Quantum\Database\Adapters\Idiorm\IdiormDbal;

class ResultIdiormTest extends IdiormDbalTestCase
{
    public function testIdiormGet(): void
    {
        $userProfileModel = new IdiormDbal('profiles');

        $users = $userProfileModel->get();

        $this->assertIsArray($users);

        $this->assertEquals('John', $users[0]->prop('firstname'));

        $this->assertEquals('Jane', $users[1]->prop('firstname'));
    }

    public function testIdiormFindOne(): void
    {
        $userProfileModel = new IdiormDbal('profiles');

        $user = $userProfileModel->findOne(1);

        $this->assertEquals('John', $user->prop('firstname'));

        $this->assertEquals('Doe', $user->prop('lastname'));
    }

    public function testIdiormFindOneBy(): void
    {
        $userProfileModel = new IdiormDbal('profiles');

        $user = $userProfileModel->findOneBy('firstname', 'John');

        $this->assertEquals('Doe', $user->prop('lastname'));

        $this->assertEquals('45', $user->prop('age'));
    }

    public function testIdiormFirst(): void
    {
        $userProfileModel = new IdiormDbal('profiles');

        $user = $userProfileModel->first();

        $this->assertEquals('Doe', $user->prop('lastname'));

        $this->assertEquals('45', $user->prop('age'));

        $userProfileModel = new IdiormDbal('profiles');

        $user = $userProfileModel->criteria('age', '<', 50)->first();

        $this->assertEquals('John', $user->prop('firstname'));

        $this->assertEquals('Doe', $user->prop('lastname'));

        $this->assertEquals('45', $user->prop('age'));
    }

    public function testIdiormCount(): void
    {
        $userProfileModel = new IdiormDbal('profiles');

        $userCount = $userProfileModel->count();

        $this->assertIsInt($userCount);

        $this->assertEquals(2, $userCount);
    }

    public function testIdiormAsArray(): void
    {
        $userProfileModel = new IdiormDbal('profiles');

        $user = $userProfileModel->first();

        $this->assertIsObject($user);

        $this->assertIsArray($user->asArray());
    }
}
