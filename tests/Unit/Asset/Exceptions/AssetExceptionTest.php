<?php

namespace Quantum\Tests\Unit\Asset\Exceptions;

use Quantum\Asset\Exceptions\AssetException;
use Quantum\Tests\Unit\AppTestCase;

class AssetExceptionTest extends AppTestCase
{
    public function testPositionInUse(): void
    {
        $exception = AssetException::positionInUse(7, 'styles.css');

        $this->assertInstanceOf(AssetException::class, $exception);
        $this->assertSame('Position `7` for asset `styles.css` is in use', $exception->getMessage());
        $this->assertSame(E_WARNING, $exception->getCode());
    }

    public function testNameInUse(): void
    {
        $exception = AssetException::nameInUse('main-style');

        $this->assertInstanceOf(AssetException::class, $exception);
        $this->assertSame('The name main-style is in use', $exception->getMessage());
        $this->assertSame(E_WARNING, $exception->getCode());
    }
}
