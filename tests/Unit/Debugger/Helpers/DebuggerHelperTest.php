<?php

namespace Quantum\Tests\Unit\Debugger\Helpers;

use Quantum\Tests\Unit\AppTestCase;
use Quantum\Debugger\Debugger;

class DebuggerHelperTest extends AppTestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function testDebugbarHelperReturnsDebuggerInstance(): void
    {
        $this->assertInstanceOf(Debugger::class, debugbar());
    }

    public function testDebugbarHelperReturnsSameInstance(): void
    {
        $first = debugbar();
        $second = debugbar();

        $this->assertSame($first, $second);
    }
}
