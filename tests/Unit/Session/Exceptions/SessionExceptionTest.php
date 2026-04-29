<?php

namespace Quantum\Tests\Unit\Session\Exceptions;

use Quantum\Session\Exceptions\SessionException;
use Quantum\Tests\Unit\AppTestCase;

class SessionExceptionTest extends AppTestCase
{
    public function testSessionNotStarted(): void
    {
        $exception = SessionException::sessionNotStarted();

        $this->assertInstanceOf(SessionException::class, $exception);
        $this->assertSame('Can not start the session.', $exception->getMessage());
        $this->assertSame(E_WARNING, $exception->getCode());
    }

    public function testSessionNotDestroyed(): void
    {
        $exception = SessionException::sessionNotDestroyed();

        $this->assertInstanceOf(SessionException::class, $exception);
        $this->assertSame('Can not destroy the session.', $exception->getMessage());
        $this->assertSame(E_WARNING, $exception->getCode());
    }
}

