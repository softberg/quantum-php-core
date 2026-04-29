<?php

namespace Quantum\Tests\Unit\View\Exceptions;

use Quantum\View\Exceptions\ViewException;
use Quantum\Tests\Unit\AppTestCase;

class ViewExceptionTest extends AppTestCase
{
    public function testNoLayoutSet(): void
    {
        $exception = ViewException::noLayoutSet();

        $this->assertInstanceOf(ViewException::class, $exception);
        $this->assertSame('Layout is not set.', $exception->getMessage());
        $this->assertSame(E_ERROR, $exception->getCode());
    }

    public function testViewNotRendered(): void
    {
        $exception = ViewException::viewNotRendered();

        $this->assertInstanceOf(ViewException::class, $exception);
        $this->assertSame('View not rendered yet.', $exception->getMessage());
        $this->assertSame(E_ERROR, $exception->getCode());
    }
}
