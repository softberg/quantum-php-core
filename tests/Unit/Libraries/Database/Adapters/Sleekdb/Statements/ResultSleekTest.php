<?php

namespace Quantum\Tests\Unit\Libraries\Database\Adapters\Sleekdb\Statements;

use Quantum\Tests\Unit\Libraries\Database\Adapters\Sleekdb\SleekDbalTestCase;
use Quantum\Libraries\Database\Adapters\Sleekdb\SleekDbal;

class ResultSleekTest extends SleekDbalTestCase
{

    private $userModel;

    public function setUp(): void
    {
        parent::setUp();

        $this->userModel = new SleekDbal('users');
    }

    public function testSleekGet()
    {
        $users = $this->userModel->get();

        $this->assertIsArray($users);

        $this->assertInstanceOf(SleekDbal::class, $users[0]);

        $this->assertNotNull($users[0]->firstname);

        $this->assertNotNull($users[0]->lastname);
    }

    public function testSleekFindOne()
    {
        $user = $this->userModel->findOne(1);

        $this->assertEquals('John', $user->prop('firstname'));

        $this->assertEquals('Doe', $user->prop('lastname'));
    }

    public function testSleekFindOneBy()
    {
        $user = $this->userModel->findOneBy('firstname', 'John');

        $this->assertEquals('Doe', $user->prop('lastname'));

        $this->assertEquals('45', $user->prop('age'));
    }

    public function testSleekFirst()
    {
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
        $this->assertIsInt($this->userModel->count());

        $this->assertEquals(2, $this->userModel->count());

        $this->userModel->criteria('age', '>', 40);

        $this->assertEquals(1, $this->userModel->count());
    }

    public function testSleekAsArray()
    {
        $user = $this->userModel->first();

        $this->assertIsObject($user);

        $this->assertIsArray($user->asArray());
    }
}