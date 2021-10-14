<?php

namespace Quantum\Test\Unit;

use PHPUnit\Framework\TestCase;
use Quantum\Libraries\Auth\User;

class UserTest extends TestCase
{

    private $user;

    private $fields = [
        'id' => ['name' => 'id', 'visible' => false],
        'firstname' => ['name' => 'firstname', 'visible' => true],
        'lastname' => ['name' => 'lastname', 'visible' => true],
        'role' => ['name' => 'role', 'visible' => true],
        'username' => ['name' => 'email', 'visible' => true],
        'password' => ['name' => 'password', 'visible' => false]
    ];

    public function setUp(): void
    {
        $this->user = new User();
    }

    public function tearDown(): void
    {
        unset($this->user);
    }

    public function testSetGetData()
    {
        $userData = ['username' => 'johny@mail.com', 'firstname' => 'Johnny', 'lastname' => 'B'];

        $this->user->setData($userData);

        $this->assertEquals($userData, $this->user->getData());
    }

    public function testSetGetFields()
    {
        $this->user->setFields($this->fields);

        $this->assertEquals(array_keys($this->user->getData()), $this->user->getFields());
    }

    public function testGetFieldsFromSetData()
    {
        $userData = ['username' => 'johny@mail.com', 'firstname' => 'Johnny', 'lastname' => 'B'];

        $this->user->setData($userData);

        $this->assertEquals(array_keys($userData), $this->user->getFields());
    }

    public function testHasField()
    {
        $this->assertFalse($this->user->hasField('username'));

        $this->user->setFieldValue('id', 21);

        $this->assertTrue($this->user->hasField('id'));

        $userData = ['username' => 'johny@mail.com', 'firstname' => 'Johnny', 'lastname' => 'B'];

        $this->user->setData($userData);

        $this->assertTrue($this->user->hasField('username'));

        $this->assertFalse($this->user->hasField('role'));

        $this->user->setFields($this->fields);

        $this->assertTrue($this->user->hasField('role'));
    }

    public function testSetGetFieldValue()
    {
        $this->assertNull($this->user->getFieldValue('email'));

        $this->user->setFieldValue('email', 'johny@mail.com');

        $this->assertEquals('johny@mail.com', $this->user->getFieldValue('email'));

        $this->user->setData(['firstname' => 'Johnny', 'lastname' => 'B']);

        $this->assertEquals('Johnny', $this->user->getFieldValue('firstname'));
    }

}