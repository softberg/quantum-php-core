<?php

namespace Quantum\Test\Unit;


use Quantum\Libraries\Database\Sleekdb\SleekDbal;

require_once dirname(__DIR__) . DS . 'SleekDbalTestCase.php';

class ResultSleekTest extends SleekDbalTestCase
{

    public function testSleekGet()
    {
        $this->userModel = new SleekDbal('users');

        $users = $this->userModel->get();

        $this->assertIsArray($users);

        $this->assertEquals('John', $users[0]['firstname']);

        $this->assertEquals('Jane', $users[1]['firstname']);

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

        $user = $this->userModel->first();

        $this->assertEquals('Doe', $user->prop('lastname'));

        $this->assertEquals('45', $user->prop('age'));

        $userModel = new SleekDbal('users');

        $user = $userModel->criteria('age', '<', 50)->first();

        $this->assertEquals('John', $user->prop('firstname'));

        $this->assertEquals('Doe', $user->prop('lastname'));

        $this->assertEquals('45', $user->prop('age'));
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
