<?php

namespace Quantum\Test\Unit;

use Mockery;
use PHPUnit\Framework\TestCase;
use Quantum\Exceptions\CsrfException;
use Quantum\Libraries\Csrf\Csrf;
use Quantum\Libraries\Session\Session;

class CsrfTest extends TestCase
{

    private $session;
    private $request;
    private $cryptor;
    private $storage = [];
    private $key = 'appkey';

    public function setUp(): void
    {

        $this->request = Mockery::mock('Quantum\Http\Request');

        $this->request->shouldReceive('getMethod')->andReturn('POST');

        $this->cryptor = Mockery::mock('Quantum\Libraries\Encryption\Cryptor');

        $this->cryptor->shouldReceive('encrypt')->andReturnUsing(function ($arg) {
            return base64_encode($arg);
        });

        $this->cryptor->shouldReceive('decrypt')->andReturnUsing(function ($arg) {
            return base64_decode($arg);
        });

        $this->session = new Session($this->storage, $this->cryptor);

        Csrf::deleteToken($this->session);
    }

    public function tearDown(): void
    {
        Mockery::close();
    }

    public function testGenerateToken()
    {
        $token = Csrf::generateToken($this->session, $this->key);

        $this->assertNotEmpty($token);

        $this->assertIsString($token);

        $this->assertEquals(Csrf::getToken($this->session), $token);
    }

    public function testGetToken()
    {
        $this->assertNull(Csrf::getToken($this->session, $this->key));

        $token = Csrf::generateToken($this->session, $this->key);

        $this->assertNotNull(Csrf::getToken($this->session, $this->key));

        $this->assertEquals($token, Csrf::getToken($this->session, $this->key));
    }

    public function testDeleteToken()
    {
        Csrf::generateToken($this->session, $this->key);

        $this->assertNotNull(Csrf::getToken($this->session, $this->key));

        Csrf::deleteToken($this->session);

        $this->assertNull(Csrf::getToken($this->session, $this->key));
    }

    public function testCheckTokenSuccss()
    {
        $this->request->shouldReceive('getCSRFToken')->andReturnUsing(function () {
            return Csrf::generateToken($this->session, $this->key);
        });

        $this->assertTrue(Csrf::checkToken($this->request, $this->session));
    }

    public function testCheckTokenMissing()
    {

        $this->request->shouldReceive('getCSRFToken')->andReturnUsing(function () {
            return null;
        });

        $this->expectException(CsrfException::class);

        $this->expectExceptionMessage(CsrfException::CSRF_TOKEN_NOT_FOUND);

        $this->assertTrue(Csrf::checkToken($this->request, $this->session));
    }

    public function testCheckTokenMismatch()
    {
        $this->request->shouldReceive('getCSRFToken')->andReturnUsing(function () {
            return 'wrong-csrf-token';
        });

        $this->expectException(CsrfException::class);

        $this->expectExceptionMessage(CsrfException::CSRF_TOKEN_NOT_MATCHED);

        $this->assertTrue(Csrf::checkToken($this->request, $this->session));
    }

}
