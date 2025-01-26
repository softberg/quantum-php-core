<?php

namespace Quantum\Tests\Libraries\Session\Helpers;

use Quantum\Libraries\Session\Session;
use Quantum\Tests\AppTestCase;

class SessionHelperFunctionsTest extends AppTestCase
{
    public function testSessionHelper()
    {
        $this->assertInstanceOf(Session::class, session());
    }

    public function testSessionMethodsViaHelper()
    {
        $this->assertFalse(session()->has('test'));

        session()->set('test', 'Testing');

        $this->assertTrue(session()->has('test'));

        $this->assertEquals('Testing', session()->get('test'));

        session()->delete('test');

        $this->assertFalse(session()->has('test'));
    }
}