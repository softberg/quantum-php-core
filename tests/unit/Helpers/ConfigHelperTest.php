<?php

namespace Quantum\Tests\Helpers;

use Quantum\Tests\AppTestCase;

class ConfigHelperTest extends AppTestCase
{

    public function setUp(): void
    {
        parent::setUp();
    }

    public function testConfigHelper()
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

    public function testGetEnvValue()
    {
        $this->assertNull(env('NEW_ENV_KEY'));

        putenv('NEW_ENV_KEY=New value');

        $this->assertEquals('New value', env('NEW_ENV_KEY'));
    }
}