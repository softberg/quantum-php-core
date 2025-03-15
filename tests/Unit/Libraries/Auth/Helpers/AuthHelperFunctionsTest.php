<?php

namespace Quantum\Tests\Unit\Libraries\Auth\Helpers;

use Quantum\Libraries\Auth\Adapters\SessionAuthAdapter;
use Quantum\Tests\Unit\AppTestCase;
use Quantum\Libraries\Auth\Auth;

class AuthHelperFunctionsTest extends AppTestCase
{

    public function testAuthHelperGetDefaultAuth()
    {
        $this->assertInstanceOf(Auth::class, auth());

        $this->assertInstanceOf(SessionAuthAdapter::class, auth()->getAdapter());
    }

    public function testAuthHelperGetSessionAuth()
    {
        $this->assertInstanceOf(Auth::class, auth(Auth::SESSION));

        $this->assertInstanceOf(SessionAuthAdapter::class, auth()->getAdapter());
    }

    public function testAuthHelperGetJwtAuth()
    {
        $this->assertInstanceOf(Auth::class, auth(Auth::JWT));

        $this->assertInstanceOf(SessionAuthAdapter::class, auth()->getAdapter());
    }
}