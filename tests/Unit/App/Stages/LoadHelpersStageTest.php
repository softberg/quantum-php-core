<?php

namespace Quantum\Tests\Unit\App\Stages;

use Quantum\App\Stages\LoadHelpersStage;
use Quantum\Tests\Unit\AppTestCase;
use Quantum\App\AppContext;
use Quantum\Di\Di;

class LoadHelpersStageTest extends AppTestCase
{
    private AppContext $context;

    public function setUp(): void
    {
        Di::reset();
        $this->context = $this->createContext();
    }

    public function tearDown(): void
    {
        Di::reset();
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
