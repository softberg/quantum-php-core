<?php

namespace Quantum\Tests\Unit\Auth;

use Quantum\Auth\Exceptions\AuthException;
use Quantum\Tests\Unit\AppTestCase;

class AuthExceptionTest extends AppTestCase
{
    public function testIncorrectCredentials(): void
    {
        $exception = AuthException::incorrectCredentials();
        $this->assertSame('Incorrect credentials.', $exception->getMessage());
        $this->assertSame(E_ERROR, $exception->getCode());
    }

    public function testInactiveAccount(): void
    {
        $exception = AuthException::inactiveAccount();
        $this->assertSame('The account is not activated.', $exception->getMessage());
        $this->assertSame(E_ERROR, $exception->getCode());
    }

    public function testIncorrectVerificationCode(): void
    {
        $exception = AuthException::incorrectVerificationCode();
        $this->assertSame('Incorrect verification code.', $exception->getMessage());
        $this->assertSame(E_ERROR, $exception->getCode());
    }

    public function testVerificationCodeExpired(): void
    {
        $exception = AuthException::verificationCodeExpired();
        $this->assertSame('Verification code expired.', $exception->getMessage());
        $this->assertSame(E_ERROR, $exception->getCode());
    }

    public function testIncorrectUserSchema(): void
    {
        $exception = AuthException::incorrectUserSchema();
        $this->assertSame('User schema does not contains all key fields.', $exception->getMessage());
        $this->assertSame(E_ERROR, $exception->getCode());
    }
}

