<?php

namespace Quantum\Tests\Unit\Csrf\Helpers;

use Quantum\Tests\Unit\AppTestCase;
use Quantum\Csrf\Csrf;

class CsrfHelperFunctionsTest extends AppTestCase
{
    private string $key = '#321dMd3QS15%';

    public function setUp(): void
    {
        parent::setUp();

        session()->delete(Csrf::TOKEN_KEY);
    }

    public function testCsrfHelperReturnsInstance(): void
    {
        $this->assertInstanceOf(Csrf::class, csrf());
    }

    public function testCsrfHelperReturnsSameInstance(): void
    {
        $this->assertSame(csrf(), csrf());
    }

    public function testCsrfTokenGeneratesToken(): void
    {
        $token = csrf()->generateToken($this->key);

        $this->assertNotEmpty($token);

        $this->assertIsString($token);

        $this->assertTrue(session()->has(Csrf::TOKEN_KEY));
    }

    public function testCsrfTokenHelperGeneratesToken(): void
    {
        $token = csrf_token();

        $this->assertNotEmpty($token);

        $this->assertIsString($token);

        $this->assertTrue(session()->has(Csrf::TOKEN_KEY));
    }

    public function testCsrfTokenHelperReturnsSameTokenOnSubsequentCalls(): void
    {
        $token1 = csrf_token();
        $token2 = csrf_token();

        $this->assertSame($token1, $token2);
    }
}
