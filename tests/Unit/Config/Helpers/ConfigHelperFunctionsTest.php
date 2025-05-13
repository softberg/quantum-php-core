<?php

namespace Quantum\Tests\Unit\Config\Helpers;

use Quantum\Tests\Unit\AppTestCase;
use Quantum\Config\Config;

class ConfigHelperFunctionsTest extends AppTestCase
{

    public function testConfigHelper()
    {
        $this->assertInstanceOf(Config::class, config());
    }

    public function testConfigHelperFunctions()
    {
        $this->assertFalse(config()->has('not-exists'));

        $this->assertEquals('Not found', config()->get('not-exists', 'Not found'));

        $this->assertEquals(config()->get('test', 'Testing'), 'Testing');

        $this->assertNull(config()->get('new-key'));

        config()->set('new-key', 'New value');

        $this->assertTrue(config()->has('new-key'));

        $this->assertEquals('New value', config()->get('new-key'));

        config()->delete('new-key');

        $this->assertFalse(config()->has('new-key'));
    }
}