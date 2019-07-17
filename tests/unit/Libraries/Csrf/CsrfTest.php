<?php

namespace Quantum\Test\Unit;

use Mockery;
use PHPUnit\Framework\TestCase;
use Quantum\Exceptions\ExceptionMessages;
use Quantum\Libraries\Csrf\Csrf;
use Quantum\Libraries\Session\Session;

class CsrfTest extends TestCase
{

    private $session;

    private $request;

    private $storage = [];

    public function setUp(): void
    {
        $this->request = Mockery::mock('Quantum\Http\Request');

        $this->request->shouldReceive('getMethod')->andReturn('POST');

        $this->session = new Session($this->storage);
    }

    public function tearDown(): void
    {
        Mockery::close();
        Csrf::deleteToken($this->session);
    }

    public function testGenerateToken()
    {
        $token = Csrf::generateToken($this->session);

        $this->assertIsString($token);

        $this->assertNotEmpty($token);

        $this->assertEquals(Csrf::getToken($this->session), $token);
    }

    public function testGetToken()
    {
        $this->assertNull(Csrf::getToken($this->session));

        $token = Csrf::generateToken($this->session);

        $this->assertNotNull(Csrf::getToken($this->session));

        $this->assertEquals($token, Csrf::getToken($this->session));

    }

    public function testDeleteToken()
    {
        Csrf::generateToken($this->session);

        $this->assertNotNull(Csrf::getToken($this->session));

        Csrf::deleteToken($this->session);

        $this->assertNull(Csrf::getToken($this->session));
    }

    public function testCheckTokenSuccss()
    {
        $this->request->shouldReceive('getCSRFToken')->andReturnUsing(function () {
            return Csrf::generateToken($this->session);
        });

        $this->assertTrue(Csrf::checkToken($this->request, $this->session));

    }

    public function testCheckTokenMissing()
    {

        $this->request->shouldReceive('getCSRFToken')->andReturnUsing(function () {
            return null;
        });

        $this->expectException(\Exception::class);

        $this->expectExceptionMessage(ExceptionMessages::CSRF_TOKEN_NOT_FOUND);

        $this->assertTrue(Csrf::checkToken($this->request, $this->session));
    }

    public function testCheckTokenMismatch()
    {
        $this->request->shouldReceive('getCSRFToken')->andReturnUsing(function () {
            return 'wrong-csrf-token';
        });

        $this->expectException(\Exception::class);

        $this->expectExceptionMessage(ExceptionMessages::CSRF_TOKEN_NOT_MATCHED);

        $this->assertTrue(Csrf::checkToken($this->request, $this->session));
    }


}