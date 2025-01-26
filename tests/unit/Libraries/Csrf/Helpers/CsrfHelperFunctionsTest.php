<?php

namespace Quantum\Tests\Libraries\Csrf\Helpers;

use Quantum\Libraries\Csrf\Csrf;
use Quantum\Tests\AppTestCase;
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