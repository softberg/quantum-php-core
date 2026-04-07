<?php

namespace Quantum\Tests\Unit\App\Stages;

use Quantum\App\Stages\RegisterCoreDependenciesStage;
use Quantum\App\Stages\LoadEnvironmentStage;
use Quantum\App\Stages\LoadHelpersStage;
use PHPUnit\Framework\TestCase;
use Quantum\App\Enums\AppType;
use Quantum\App\AppContext;
use Quantum\App\App;
use Quantum\Di\Di;

class LoadEnvironmentStageTest extends TestCase
{
    public function setUp(): void
    {
        Di::reset();
        App::setBaseDir(PROJECT_ROOT);

        $context = new AppContext(AppType::WEB);

        (new RegisterCoreDependenciesStage())->process($context);
        (new LoadHelpersStage())->process($context);
    }

    public function tearDown(): void
    {
        Di::reset();
    }

    public function testLoadEnvironmentStageLoadsEnvVars(): void
    {
        $stage = new LoadEnvironmentStage();
        $stage->process(new AppContext(AppType::WEB));

        $this->assertNotEmpty(env('APP_KEY'));
    }

    public function testLoadEnvironmentStageSetsMutableForConsole(): void
    {
        $stage = new LoadEnvironmentStage();
        $stage->process(new AppContext(AppType::CONSOLE));

        $this->assertNotEmpty(env('APP_KEY'));
    }
}
