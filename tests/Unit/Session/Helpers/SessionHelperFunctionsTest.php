<?php

namespace Quantum\Tests\Unit\Session\Helpers;

use Quantum\Session\Adapters\Database\DatabaseSessionAdapter;
use Quantum\Session\Adapters\Native\NativeSessionAdapter;
use Quantum\Tests\Unit\AppTestCase;
use Quantum\Session\Session;

class SessionHelperFunctionsTest extends AppTestCase
{
    public function testSessionHelperGetDefaultSessionAdapter(): void
    {
        config()->delete('session');

        $session = session();

        $this->assertInstanceOf(Session::class, $session);

        $this->assertInstanceOf(NativeSessionAdapter::class, $session->getAdapter());
    }

    public function testSessionHelperGetNativeSessionAdapter(): void
    {
        $session = session(Session::NATIVE);

        $this->assertInstanceOf(Session::class, $session);

        $this->assertInstanceOf(NativeSessionAdapter::class, $session->getAdapter());
    }

    public function testSessionHelperGetDatabaseSessionAdapter(): void
    {
        $session = session(Session::DATABASE);

        $this->assertInstanceOf(Session::class, $session);

        $this->assertInstanceOf(DatabaseSessionAdapter::class, $session->getAdapter());
    }

    public function testSessionMethodsViaHelper(): void
    {
        $this->assertFalse(session()->has('test'));

        session()->set('test', 'Testing');

        $this->assertTrue(session()->has('test'));

        $this->assertEquals('Testing', session()->get('test'));

        session()->delete('test');

        $this->assertFalse(session()->has('test'));
    }
}
