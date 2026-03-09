<?php

namespace Quantum\Tests\Unit\View;

use Quantum\Tests\Unit\AppTestCase;
use Quantum\View\RawParam;

class RawParamTest extends AppTestCase
{
    public function testStoresAndReturnsString(): void
    {
        $raw = new RawParam('test');
        $this->assertSame('test', $raw->getValue());
    }

    public function testStoresAndReturnsInteger(): void
    {
        $raw = new RawParam(123);
        $this->assertSame(123, $raw->getValue());
    }

    public function testStoresAndReturnsArray(): void
    {
        $value = ['key' => 'value'];
        $raw = new RawParam($value);
        $this->assertSame($value, $raw->getValue());
    }

    public function testStoresAndReturnsObject(): void
    {
        $value = (object) ['foo' => 'bar'];
        $raw = new RawParam($value);
        $this->assertSame($value, $raw->getValue());
    }

    public function testStoresAndReturnsNull(): void
    {
        $raw = new RawParam(null);
        $this->assertNull($raw->getValue());
    }

    public function testStoresAndReturnsBoolean(): void
    {
        $rawTrue = new RawParam(true);
        $rawFalse = new RawParam(false);

        $this->assertTrue($rawTrue->getValue());
        $this->assertFalse($rawFalse->getValue());
    }
}
