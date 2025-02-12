<?php

namespace Quantum\Tests\Unit\Libraries\Session;

use Quantum\Libraries\Session\Adapters\Database\DatabaseSessionAdapter;
use Quantum\Libraries\Session\Adapters\Native\NativeSessionAdapter;
use Quantum\Libraries\Session\Contracts\SessionStorageInterface;
use Quantum\Libraries\Session\Exceptions\SessionException;
use Quantum\Libraries\Session\Session;
use Quantum\Tests\Unit\AppTestCase;
use Quantum\Loader\Setup;

class SessionTest extends AppTestCase
{

    public function setUp(): void
    {
        parent::setUp();

        if (!config()->has('session')) {
            config()->import(new Setup('config', 'session'));
        }
    }

    public function testSessionGetAdapter()
    {
        $session = new Session(new NativeSessionAdapter());

        $this->assertInstanceOf(NativeSessionAdapter::class, $session->getAdapter());

        $this->assertInstanceOf(SessionStorageInterface::class, $session->getAdapter());

        $session = new Session(new DatabaseSessionAdapter());

        $this->assertInstanceOf(DatabaseSessionAdapter::class, $session->getAdapter());

        $this->assertInstanceOf(SessionStorageInterface::class, $session->getAdapter());
    }

    public function testSessionCallingValidMethod()
    {
        $session = new Session(new NativeSessionAdapter());

        $session->set('test', 'Test data');

        $this->assertEquals('Test data', $session->get('test'));
    }

    public function testSessionCallingInvalidMethod()
    {
        $mailer = new Session(new NativeSessionAdapter());

        $this->expectException(SessionException::class);

        $this->expectExceptionMessage('The method `callingInvalidMethod` is not supported for `' . NativeSessionAdapter::class . '`');

        $mailer->callingInvalidMethod();
    }
}