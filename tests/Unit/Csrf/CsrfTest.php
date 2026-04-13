<?php

namespace Quantum\Tests\Unit\Csrf;

use Quantum\Csrf\Exceptions\CsrfException;
use Quantum\Csrf\Enums\ExceptionMessages;
use Quantum\Tests\Unit\AppTestCase;
use Quantum\Http\Request;
use Quantum\Csrf\Csrf;

class CsrfTest extends AppTestCase
{
    private Csrf $csrf;
    private Request $request;
    private string $key = '#321dMd3QS15%';

    public function setUp(): void
    {
        parent::setUp();

        $this->request = request();

        $this->csrf = csrf();
    }

    public function testGenerateToken(): void
    {
        $token = $this->csrf->generateToken($this->key);

        $this->assertNotEmpty($token);

        $this->assertIsString($token);

        $this->assertTrue(session()->has(Csrf::TOKEN_KEY));

        $this->assertEquals($token, session()->has(Csrf::TOKEN_KEY));
    }

    public function testCheckTokenSuccess(): void
    {
        $token = $this->csrf->generateToken($this->key);

        $this->request->create('POST', '/submit', [
            'firstname' => 'Josn',
            'lastname' => 'Doe',
            'csrf-token' => $token,
        ]);

        $this->assertTrue($this->csrf->checkToken($this->request));
    }

    public function testCheckTokenMissing(): void
    {
        $this->csrf->generateToken($this->key);

        $this->request->create('POST', '/submit', ['firstname' => 'Josn', 'lastname' => 'Doe']);

        $this->expectException(CsrfException::class);

        $this->expectExceptionMessage(ExceptionMessages::CSRF_TOKEN_MISSING);

        $this->assertTrue($this->csrf->checkToken($this->request));
    }

    public function testCheckTokenMismatch(): void
    {
        $this->csrf->generateToken($this->key);

        $this->request->create('POST', '/submit', [
            'firstname' => 'Josn',
            'lastname' => 'Doe',
            'csrf-token' => 'wrong-csrf-token',
        ]);

        $this->expectException(CsrfException::class);

        $this->expectExceptionMessage(ExceptionMessages::CSRF_TOKEN_MISMATCH);

        $this->assertTrue($this->csrf->checkToken($this->request));
    }

}
