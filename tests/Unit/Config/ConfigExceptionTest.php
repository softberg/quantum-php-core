<?php

namespace Quantum\Tests\Unit\Config;

use Quantum\Config\Exceptions\ConfigException;
use Quantum\Tests\Unit\AppTestCase;

class ConfigExceptionTest extends AppTestCase
{
    public function testConfigCollision(): void
    {
        $exception = ConfigException::configCollision('database');

        $this->assertInstanceOf(ConfigException::class, $exception);
        $this->assertSame('Config key `database` is already in use', $exception->getMessage());
        $this->assertSame(E_WARNING, $exception->getCode());
    }
}

