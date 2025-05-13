<?php

namespace Quantum\Tests\Unit\Environment\Helpers;

use Quantum\Tests\Unit\AppTestCase;

class EnvHelperTest extends AppTestCase
{
    public function testGetEnvValue()
    {
        $this->assertNull(env('NEW_ENV_KEY'));

        putenv('NEW_ENV_KEY=New value');

        $this->assertEquals('New value', env('NEW_ENV_KEY'));
    }
}
