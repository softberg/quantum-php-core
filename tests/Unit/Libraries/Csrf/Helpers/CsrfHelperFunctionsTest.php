<?php

namespace Quantum\Tests\Unit\Libraries\Csrf\Helpers;

use Quantum\Tests\Unit\AppTestCase;
use Quantum\Libraries\Csrf\Csrf;
use Quantum\Http\Request;

class CsrfHelperFunctionsTest extends AppTestCase
{
    public function testCsrfHelper()
    {
        $this->assertInstanceOf(Csrf::class, csrf());
    }

    public function testCsrfToken()
    {
        $request = new Request();

        $request->create('PUT', '/update', ['title' => 'Task Title', 'csrf-token' => csrf_token()]);

        $this->assertTrue(csrf()->checkToken($request));
    }
}