<?php

namespace Quantum\Tests\Libraries\Session\Adapters\Native;

use Quantum\Libraries\Session\Adapters\Native\NativeSessionAdapter;
use Quantum\Tests\AppTestCase;

class NativeSessionAdapterTest extends AppTestCase
{

    private $session;

    public function setUp(): void
    {
        parent::setUp();

        $this->session = new NativeSessionAdapter();

        $this->session->delete('LAST_ACTIVITY');
    }

    public function testNativeSessionConstructor()
    {
        $this->assertInstanceOf(NativeSessionAdapter::class, $this->session);
    }

    public function testNativeSessionSessionAll()
    {
        $this->assertEmpty($this->session->all());

        $this->session->set('test', 'Test data');

        $this->session->set('user', ['username' => 'test@unit.com']);

        $this->assertNotEmpty($this->session->all());

        $this->assertIsArray($this->session->all());

        $this->assertArrayHasKey('test', $this->session->all());

        $this->assertEquals('Test data', $this->session->all()['test']);
    }

    public function testNativeSessionGetSetHasDelete()
    {
        $this->assertNull($this->session->get('auth'));

        $this->assertFalse($this->session->has('auth'));

        $this->session->set('auth', 'Authenticated');

        $this->assertTrue($this->session->has('auth'));

        $this->assertEquals('Authenticated', $this->session->get('auth'));

        $this->session->delete('auth');

        $this->assertFalse($this->session->has('auth'));

        $this->assertNull($this->session->get('auth'));
    }

    public function testNativeSessionGetSetFlash()
    {
        $this->session->setFlash('message', 'Flash message');

        $this->assertEquals('Flash message', $this->session->getFlash('message'));

        $this->assertNull($this->session->getFlash('message'));
    }

    public function testNativeSessionFlush()
    {
        $this->session->set('test', 'Test data');

        $this->assertNotEmpty($this->session->all());

        $this->session->flush();

        $this->assertEmpty($this->session->all());

        session_start();
    }

    public function testNativeSessionGetSessionId()
    {
        $this->assertEquals(session_id(), $this->session->getId());
    }

    public function testNativeSessionRegenerateSessionId()
    {
        $sessionId = $this->session->getId();

        $this->assertEquals(session_id(), $sessionId);

        $this->session->regenerateId();

        $this->assertNotEquals(session_id(), $sessionId);
    }
}