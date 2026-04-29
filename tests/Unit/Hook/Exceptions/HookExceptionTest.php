<?php

namespace Quantum\Tests\Unit\Hook\Exceptions;

use Quantum\Hook\Exceptions\HookException;
use Quantum\Tests\Unit\AppTestCase;

class HookExceptionTest extends AppTestCase
{
    public function testHookDuplicateName(): void
    {
        $exception = HookException::hookDuplicateName('boot');

        $this->assertInstanceOf(HookException::class, $exception);
        $this->assertSame('The Hook `boot` already registered.', $exception->getMessage());
        $this->assertSame(E_ERROR, $exception->getCode());
    }

    public function testUnregisteredHookName(): void
    {
        $exception = HookException::unregisteredHookName('shutdown');

        $this->assertInstanceOf(HookException::class, $exception);
        $this->assertSame('The Hook `shutdown` was not registered.', $exception->getMessage());
        $this->assertSame(E_WARNING, $exception->getCode());
    }
}

