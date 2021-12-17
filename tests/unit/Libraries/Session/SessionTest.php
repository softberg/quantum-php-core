<?php

namespace Quantum\Tests\Libraries\Session;

use Mockery;
use PHPUnit\Framework\TestCase;
use Quantum\Libraries\Session\Session;

class SessionTest extends TestCase
{

    private $session;
    
    private $cryptor;

    private $sessionData = [
        'auth' => 'b2s=', // ok
        'test' => 'Z29vZA==', // good
        'store' => 'cGVyc2lzdA==' // persist
    ];

    public function setUp(): void
    {
        $this->cryptor = Mockery::mock('Quantum\Libraries\Encryption\Cryptor');

        $this->cryptor->shouldReceive('encrypt')->andReturnUsing(function ($arg) {
            return base64_encode($arg);
        });

        $this->cryptor->shouldReceive('decrypt')->andReturnUsing(function ($arg) {
            return base64_decode($arg);
        });

        $this->session = new Session($this->sessionData, $this->cryptor);
    }

    public function testSessionConstructor()
    {
        $this->assertInstanceOf('Quantum\Libraries\Session\Session', $this->session);
    }

    public function testSessionGet()
    {
        $this->assertEquals('ok', $this->session->get('auth'));

        $this->assertNull($this->session->get('not-exists'));
    }

    public function testSessionAll()
    {
        $this->assertEquals(array_map('base64_decode', $this->sessionData), $this->session->all());
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
        $this->assertEquals(array_map('base64_decode', $this->sessionData), $this->session->all());

        $this->session->flush();

        $this->assertEmpty($this->session->all());
    }

}
