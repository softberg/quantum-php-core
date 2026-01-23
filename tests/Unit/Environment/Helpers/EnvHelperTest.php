<?php

namespace Quantum\Tests\Unit\Environment\Helpers;

use Quantum\Tests\Unit\AppTestCase;

class EnvHelperTest extends AppTestCase
{
    public function testGetEnvValue()
    {
        $this->assertNotNull(env('APP_KEY'));

        $this->assertNotNull(env('DEBUG'));

        $this->assertEquals('TRUE', env('DEBUG'));
    }
}
