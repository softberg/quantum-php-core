<?php

namespace Quantum\Tests\Libraries\Auth\Helpers;

use Quantum\Libraries\Auth\Auth;
use Quantum\Tests\AppTestCase;

class AuthHelperFunctionsTest extends AppTestCase
{

    public function testAuthHelper()
    {
        $this->assertInstanceOf(Auth::class, auth());
    }
}