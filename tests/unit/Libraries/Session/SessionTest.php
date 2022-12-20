<?php

namespace Quantum\Tests\Libraries\Session;

use Quantum\Libraries\Session\Session;
use Quantum\Loader\Setup;
use Quantum\Tests\AppTestCase;

class SessionTest extends AppTestCase
{

    private $session;

    private $srotage = [];

    public function setUp(): void
    {
        parent::setUp();

        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $this->session = Session::getInstance($this->srotage);
    }

    public function tearDown(): void
    {
        $this->session->flush();
    }

    public function testSessionConstructor()
    {
        $this->assertInstanceOf(Session::class, $this->session);
    }

    public function testSessionAll()
    {
        $this->assertEmpty($this->session->all());

        $this->session->set('test', 'Test data');

        $this->session->set('user', ['username' => 'test@unit.com']);

        $this->assertNotEmpty($this->session->all());

        $this->assertIsArray($this->session->all());

        $this->assertArrayHasKey('test', $this->session->all());
    }

    public function testSessionGetSetHasDelete()
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

    public function testGetSetFlash()
    {
        $this->session->setFlash('message', 'Flash message');

        $this->assertEquals('Flash message', $this->session->getFlash('message'));

        $this->assertNull($this->session->getFlash('message'));
    }

    public function testSessionFlush()
    {
        $this->session->set('test', 'Test data');

        $this->assertNotEmpty($this->session->all());

        $this->session->flush();

        $this->assertEmpty($this->session->all());

        session_start();
    }

    public function testGetSessionId()
    {
        $this->assertEquals(session_id(), $this->session->getId());
    }

    public function testRegenerateSessionId()
    {
        $sessionId = $this->session->getId();

        $this->assertEquals(session_id(), $sessionId);

        $this->session->regenerateId();

        $this->assertNotEquals(session_id(), $sessionId);
    }

}
