<?php

namespace Quantum\Tests\Unit\Session;

use Quantum\Session\Adapters\Database\DatabaseSessionAdapter;
use Quantum\Session\Adapters\Native\NativeSessionAdapter;
use Quantum\Session\Contracts\SessionStorageInterface;
use Quantum\Session\Exceptions\SessionException;
use Quantum\Tests\Unit\AppTestCase;
use Quantum\Session\Session;
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

    public function testSessionGetAdapter(): void
    {
        $session = new Session(new NativeSessionAdapter());

        $this->assertInstanceOf(NativeSessionAdapter::class, $session->getAdapter());

        $this->assertInstanceOf(SessionStorageInterface::class, $session->getAdapter());

        $session = new Session(new DatabaseSessionAdapter());

        $this->assertInstanceOf(DatabaseSessionAdapter::class, $session->getAdapter());

        $this->assertInstanceOf(SessionStorageInterface::class, $session->getAdapter());
    }

    public function testSessionCallingValidMethod(): void
    {
        $session = new Session(new NativeSessionAdapter());

        $session->set('test', 'Test data');

        $this->assertEquals('Test data', $session->get('test'));
    }

    public function testSessionCallingInvalidMethod(): void
    {
        $mailer = new Session(new NativeSessionAdapter());

        $this->expectException(SessionException::class);

        $this->expectExceptionMessage('The method `callingInvalidMethod` is not supported for `' . NativeSessionAdapter::class . '`');

        $mailer->callingInvalidMethod();
    }
}
