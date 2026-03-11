<?php

namespace Quantum\Tests\Unit\Csrf\Helpers;

use Quantum\Tests\Unit\AppTestCase;
use Quantum\Http\Request;
use Quantum\Csrf\Csrf;

class CsrfHelperFunctionsTest extends AppTestCase
{
    public function testCsrfHelper(): void
    {
        $this->assertInstanceOf(Csrf::class, csrf());
    }

    public function testCsrfToken(): void
    {
        $request = new Request();

        $request->create('PUT', '/update', ['title' => 'Task Title', 'csrf-token' => csrf_token()]);

        $this->assertTrue(csrf()->checkToken($request));
    }
}
