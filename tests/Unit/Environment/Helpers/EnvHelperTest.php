<?php

namespace Quantum\Tests\Unit\Environment\Helpers;

use Quantum\Environment\Environment;
use Quantum\Tests\Unit\AppTestCase;

class EnvHelperTest extends AppTestCase
{
    public function testEnvironmentHelperReturnsInstance(): void
    {
        $this->assertInstanceOf(Environment::class, environment());
    }

    public function testEnvironmentHelperReturnsSameInstance(): void
    {
        $this->assertSame(environment(), environment());
    }

    public function testGetEnvValue(): void
    {
        $this->assertNotNull(env('APP_KEY'));

        $this->assertNotNull(env('DEBUG'));

        $this->assertEquals('TRUE', env('DEBUG'));
    }

    public function testGetEnvValueWithDefault(): void
    {
        $this->assertNull(env('NON_EXISTING'));
        $this->assertEquals('fallback', env('NON_EXISTING', 'fallback'));
    }
}
