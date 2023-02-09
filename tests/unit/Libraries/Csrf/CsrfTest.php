<?php

namespace Quantum\Tests\Libraries\Csrf;

use Quantum\Exceptions\CsrfException;
use Quantum\Libraries\Csrf\Csrf;
use Quantum\Tests\AppTestCase;
use Quantum\Http\Request;

class CsrfTest extends AppTestCase
{

    private $csrf;
    private $request;
    private $key = '#321dMd3QS15%';

    public function setUp(): void
    {
        parent::setUp();

        $this->request = new Request();

        $this->csrf = Csrf::getInstance();
    }

    public function testGenerateToken()
    {
        $token = $this->csrf->generateToken($this->key);

        $this->assertNotEmpty($token);

        $this->assertIsString($token);

        $this->assertTrue(session()->has(Csrf::TOKEN_KEY));

        $this->assertEquals($token, session()->has(Csrf::TOKEN_KEY));
    }

    public function testCheckTokenSuccess()
    {
        $token = $this->csrf->generateToken($this->key);

        $this->request->create('POST', '/submit', [
            'firstname' => 'Josn',
            'lastname' => 'Doe',
            'csrf-token' => $token
        ]);

        $this->assertTrue($this->csrf->checkToken($this->request));
    }

    public function testCheckTokenMissing()
    {
        $this->csrf->generateToken($this->key);

        $this->request->create('POST', '/submit', ['firstname' => 'Josn', 'lastname' => 'Doe']);

        $this->expectException(CsrfException::class);

        $this->expectExceptionMessage('csrf_token_not_found');

        $this->assertTrue($this->csrf->checkToken($this->request));
    }

    public function testCheckTokenMismatch()
    {
        $this->csrf->generateToken($this->key);

        $this->request->create('POST', '/submit', [
            'firstname' => 'Josn',
            'lastname' => 'Doe',
            'csrf-token' => 'wrong-csrf-token'
        ]);

        $this->expectException(CsrfException::class);

        $this->expectExceptionMessage('csrf_token_not_matched');

        $this->assertTrue($this->csrf->checkToken($this->request));
    }

}
