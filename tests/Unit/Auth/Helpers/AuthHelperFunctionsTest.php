<?php

namespace Quantum\Tests\Unit\Auth\Helpers;

use Quantum\Auth\Adapters\SessionAuthAdapter;
use Quantum\Auth\Adapters\JwtAuthAdapter;
use Quantum\Tests\Unit\AppTestCase;
use Quantum\Auth\Enums\AuthType;
use Quantum\Auth\Auth;

class AuthHelperFunctionsTest extends AppTestCase
{
    public function testAuthHelperGetDefaultAuth(): void
    {
        $this->assertInstanceOf(Auth::class, auth());

        $this->assertInstanceOf(SessionAuthAdapter::class, auth()->getAdapter());
    }

    public function testAuthHelperGetSessionAuth(): void
    {
        $this->assertInstanceOf(Auth::class, auth(AuthType::SESSION));

        $this->assertInstanceOf(SessionAuthAdapter::class, auth(AuthType::SESSION)->getAdapter());
    }

    public function testAuthHelperGetJwtAuth(): void
    {
        $this->assertInstanceOf(Auth::class, auth(AuthType::JWT));

        $this->assertInstanceOf(JwtAuthAdapter::class, auth(AuthType::JWT)->getAdapter());
    }
}
