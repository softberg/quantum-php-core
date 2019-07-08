<?php

namespace Quantum\Test\Unit;

use Mockery;
use PHPUnit\Framework\TestCase;
use Quantum\Libraries\Session\Session;
use Quantum\Libraries\Session\SessionStorage;

class SessionTest extends TestCase
{

    private $session;

    private $sessionStorage;

    private $sessionData = [
        'auth' => 'ok',
        'test' => 'good',
        'store' => 'persist'
    ];

    public function setUp(): void
    {
        $this->sessionStorage = new SessionStorage($this->sessionData);

        $this->session = new Session($this->sessionStorage);
    }

    public function testSessionConstructor()
    {
        $this->assertInstanceOf('Quantum\Libraries\Session\SessionStorage', $this->sessionStorage);

        $this->assertInstanceOf('Quantum\Libraries\Session\Session', $this->session);
    }


    public function testSessionGet()
    {
        $this->assertEquals('ok', $this->session->get('auth'));

        $this->assertNull($this->session->get('not-exists'));
    }

    public function testSessionAll()
    {
        $this->assertEquals($this->sessionData, $this->session->all());
    }

    public function testSessionHas()
    {
        $this->assertFalse($this->session->has('not-exists'));

        $this->assertTrue($this->session->has('test'));
    }

    public function testSessionSet()
    {
        $this->session->set('new', 'New Value');

        $this->assertTrue($this->session->has('new'));

        $this->assertEquals('New Value', $this->session->get('new'));
    }

    public function testFlash()
    {
        $this->session->setFlash('new', 'New Value');

        $this->assertEquals('New Value', $this->session->getFlash('new'));

        $this->assertNull($this->session->getFlash('new'));
    }

    public function testSessionDelete()
    {
        $this->assertTrue($this->session->has('test'));

        $this->session->delete('test');

        $this->assertFalse($this->session->has('test'));
    }

    public function testSessionFlush()
    {
        $this->assertEquals($this->sessionData, $this->session->all());

        $this->session->flush();

        $this->assertEmpty($this->session->all());
    }

}