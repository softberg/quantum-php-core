<?php

namespace Quantum\Tests\Unit\Libraries\Auth\Helpers;

use Quantum\Tests\Unit\AppTestCase;
use Quantum\Libraries\Auth\Auth;

class AuthHelperFunctionsTest extends AppTestCase
{

    public function testAuthHelper()
    {
        $this->assertInstanceOf(Auth::class, auth());
    }
}