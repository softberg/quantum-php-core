<?php

namespace Quantum\Tests\Unit\App;

use Quantum\App\Enums\AppType;
use Quantum\App\AppContext;
use PHPUnit\Framework\TestCase;
use InvalidArgumentException;

class AppContextTest extends TestCase
{
    public function testAppContextWebMode(): void
    {
        $context = new AppContext(AppType::WEB);

        $this->assertSame(AppType::WEB, $context->getMode());
        $this->assertTrue($context->isWebMode());
        $this->assertFalse($context->isConsoleMode());
    }

    public function testAppContextConsoleMode(): void
    {
        $context = new AppContext(AppType::CONSOLE);

        $this->assertSame(AppType::CONSOLE, $context->getMode());
        $this->assertFalse($context->isWebMode());
        $this->assertTrue($context->isConsoleMode());
    }

    public function testAppContextRejectsInvalidMode(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid app mode: invalid');

        new AppContext('invalid');
    }
}
