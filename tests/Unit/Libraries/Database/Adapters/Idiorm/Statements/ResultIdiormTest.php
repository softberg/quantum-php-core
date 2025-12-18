<?php

namespace Quantum\Tests\Unit\Libraries\Database\Adapters\Idiorm\Statements;

use Quantum\Tests\Unit\Libraries\Database\Adapters\Idiorm\IdiormDbalTestCase;
use Quantum\Libraries\Database\Adapters\Idiorm\IdiormDbal;

class ResultIdiormTest extends IdiormDbalTestCase
{

    public function testIdiormGet()
    {
        $userProfileModel = new IdiormDbal('profiles');

        $users = $userProfileModel->get();

        $this->assertIsArray($users);

        $this->assertEquals('John', $users[0]->prop('firstname'));

        $this->assertEquals('Jane', $users[1]->prop('firstname'));
    }

    public function testIdiormFindOne()
    {
        $userProfileModel = new IdiormDbal('profiles');

        $user = $userProfileModel->findOne(1);

        $this->assertEquals('John', $user->prop('firstname'));

        $this->assertEquals('Doe', $user->prop('lastname'));
    }

    public function testIdiormFindOneBy()
    {
        $userProfileModel = new IdiormDbal('profiles');

        $user = $userProfileModel->findOneBy('firstname', 'John');

        $this->assertEquals('Doe', $user->prop('lastname'));

        $this->assertEquals('45', $user->prop('age'));
    }

    public function testIdiormFirst()
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

    public function testIdiormCount()
    {
        $userProfileModel = new IdiormDbal('profiles');

        $userCount = $userProfileModel->count();

        $this->assertIsInt($userCount);

        $this->assertEquals(2, $userCount);
    }

    public function testIdiormAsArray()
    {
        $userProfileModel = new IdiormDbal('profiles');

        $user = $userProfileModel->first();

        $this->assertIsObject($user);

        $this->assertIsArray($user->asArray());
    }
}
