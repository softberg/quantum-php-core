<?php

namespace Quantum\Tests\Unit\Libraries\Config\Helpers;

use Quantum\Libraries\Config\Config;
use Quantum\Tests\Unit\AppTestCase;

class ConfigHelperFunctionsTest extends AppTestCase
{

    public function testConfigHelper()
    {
        $this->assertInstanceOf(Config::class, config());
    }
}