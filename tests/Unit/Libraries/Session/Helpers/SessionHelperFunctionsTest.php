<?php

namespace Quantum\Tests\Unit\Libraries\Session\Helpers;

use Quantum\Libraries\Session\Adapters\Database\DatabaseSessionAdapter;
use Quantum\Libraries\Session\Adapters\Native\NativeSessionAdapter;
use Quantum\Libraries\Session\Session;
use Quantum\Tests\Unit\AppTestCase;

class SessionHelperFunctionsTest extends AppTestCase
{
    public function testSessionHelperGetDefaultSessionAdapter()
    {
        config()->delete('session');

        $session = session();

        $this->assertInstanceOf(Session::class, $session);

        $this->assertInstanceOf(NativeSessionAdapter::class, $session->getAdapter());
    }

    public function testSessionHelperGetNativeSessionAdapter()
    {
        $session = session(Session::NATIVE);

        $this->assertInstanceOf(Session::class, $session);

        $this->assertInstanceOf(NativeSessionAdapter::class, $session->getAdapter());
    }

    public function testSessionHelperGetDatabaseSessionAdapter()
    {
        $session = session(Session::DATABASE);

        $this->assertInstanceOf(Session::class, $session);

        $this->assertInstanceOf(DatabaseSessionAdapter::class, $session->getAdapter());
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