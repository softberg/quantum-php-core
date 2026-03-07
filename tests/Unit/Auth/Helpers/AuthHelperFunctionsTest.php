<?php

namespace Quantum\Tests\Unit\Auth\Helpers;

use Quantum\Auth\Adapters\SessionAuthAdapter;
use Quantum\Auth\Adapters\JwtAuthAdapter;
use Quantum\Tests\Unit\AppTestCase;
use Quantum\Auth\Auth;

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

        $this->assertInstanceOf(SessionAuthAdapter::class, auth(Auth::SESSION)->getAdapter());
    }

    public function testAuthHelperGetJwtAuth()
    {
        $this->assertInstanceOf(Auth::class, auth(Auth::JWT));

        $this->assertInstanceOf(JwtAuthAdapter::class, auth(Auth::JWT)->getAdapter());
    }
}
