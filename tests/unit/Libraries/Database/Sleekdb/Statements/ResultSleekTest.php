<?php

namespace Quantum\Tests\Libraries\Database\Sleekdb\Statements;

use Quantum\Tests\Libraries\Database\Sleekdb\SleekDbalTestCase;
use Quantum\Libraries\Database\Sleekdb\SleekDbal;

class ResultSleekTest extends SleekDbalTestCase
{

    public function testSleekGet()
    {
        $this->userModel = new SleekDbal('users');

        $users = $this->userModel->get();

        $this->assertIsArray($users);

        $this->assertInstanceOf(SleekDbal::class, $users[0]);

        $this->assertNotNull($users[0]->firstname);

        $this->assertNotNull($users[0]->lastname);
    }

    public function testSleekFindOne()
    {
        $this->userModel = new SleekDbal('users');

        $user = $this->userModel->findOne(1);

        $this->assertEquals('John', $user->prop('firstname'));

        $this->assertEquals('Doe', $user->prop('lastname'));
    }

    public function testSleekFindOneBy()
    {
        $this->userModel = new SleekDbal('users');

        $user = $this->userModel->findOneBy('firstname', 'John');

        $this->assertEquals('Doe', $user->prop('lastname'));

        $this->assertEquals('45', $user->prop('age'));
    }

    public function testSleekFirst()
    {
        $this->userModel = new SleekDbal('users');

        $user = $this->userModel->criteria('age', '>', 40)->first();

        $this->assertEquals('Doe', $user->prop('lastname'));

        $this->assertEquals('45', $user->prop('age'));

        $userModel = new SleekDbal('users');

        $user = $userModel->criteria('age', '<', 40)->first();

        $this->assertEquals('Jane', $user->prop('firstname'));

        $this->assertEquals('Du', $user->prop('lastname'));

        $this->assertEquals('35', $user->prop('age'));
    }

    public function testSleekCount()
    {
        $this->userModel = new SleekDbal('users');

        $this->assertIsInt($this->userModel->count());

        $this->assertEquals(2, $this->userModel->count());

        $this->userModel->criteria('age', '>', 40);

        $this->assertEquals(1, $this->userModel->count());
    }

    public function testSleekAsArray()
    {
        $userModel = new SleekDbal('users');

        $user = $userModel->first();

        $this->assertIsObject($user);

        $this->assertIsArray($user->asArray());
    }
}
