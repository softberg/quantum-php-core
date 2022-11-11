<?php

namespace Quantum\Tests\Libraries\Csrf;

use Quantum\Libraries\Session\Session;
use Quantum\Exceptions\CsrfException;
use Quantum\Libraries\Csrf\Csrf;
use Quantum\Tests\AppTestCase;
use Mockery;

class CsrfTest extends AppTestCase
{

    private $session;
    private $request;
    private $storage = [];
    private $key = 'appkey';

    public function setUp(): void
    {
        parent::setUp();

        $this->request = Mockery::mock('Quantum\Http\Request');

        $this->request->shouldReceive('getMethod')->andReturn('POST');

        $this->session = Session::getInstance($this->storage);

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

        $this->expectExceptionMessage('csrf_token_not_found');

        $this->assertTrue(Csrf::checkToken($this->request, $this->session));
    }

    public function testCheckTokenMismatch()
    {
        $this->request->shouldReceive('getCSRFToken')->andReturnUsing(function () {
            return 'wrong-csrf-token';
        });

        $this->expectException(CsrfException::class);

        $this->expectExceptionMessage('csrf_token_not_matched');

        $this->assertTrue(Csrf::checkToken($this->request, $this->session));
    }

}
