<?php

namespace Quantum\Tests\Libraries\Config\Helpers;

use Quantum\Libraries\Config\Config;
use Quantum\Tests\AppTestCase;

class ConfigHelperFunctionsTest extends AppTestCase
{

    public function testConfigHelper()
    {
        $this->assertInstanceOf(Config::class, config());
    }
}