<?php

namespace Quantum\Tests\Unit\App\Stages;

use Quantum\App\Stages\LoadHelpersStage;
use Quantum\Tests\Unit\AppTestCase;

class LoadHelpersStageTest extends AppTestCase
{
    public function setUp(): void
    {
        $this->context = $this->createContext();
    }

    public function tearDown(): void
    {
        $this->clearAppContext();
    }

    public function testLoadHelpersStageLoadsComponentHelpers(): void
    {
        $stage = new LoadHelpersStage();
        $stage->process($this->context);

        $this->assertTrue(function_exists('config'));
        $this->assertTrue(function_exists('env'));
        $this->assertTrue(function_exists('base_dir'));
    }
}
