<?php

namespace Quantum\Tests\Unit\App\Stages;

use Quantum\App\Stages\RegisterCoreDependenciesStage;
use Quantum\App\Stages\LoadHelpersStage;
use Quantum\App\Enums\AppType;
use Quantum\App\AppContext;
use Quantum\App\App;
use PHPUnit\Framework\TestCase;
use Quantum\Di\Di;

class LoadHelpersStageTest extends TestCase
{
    public function setUp(): void
    {
        Di::reset();
        App::setBaseDir(PROJECT_ROOT);

        $depsStage = new RegisterCoreDependenciesStage();
        $depsStage->process(new AppContext(AppType::WEB));
    }

    public function tearDown(): void
    {
        Di::reset();
    }

    public function testLoadHelpersStageLoadsComponentHelpers(): void
    {
        $stage = new LoadHelpersStage();
        $stage->process(new AppContext(AppType::WEB));

        $this->assertTrue(function_exists('config'));
        $this->assertTrue(function_exists('env'));
        $this->assertTrue(function_exists('base_dir'));
    }
}
