<?php

namespace Quantum\Tests\Unit\App\Stages;

use Quantum\App\Stages\LoadEnvironmentStage;
use Quantum\App\Stages\LoadHelpersStage;
use Quantum\Tests\Unit\AppTestCase;
use Quantum\App\Enums\AppType;
use Quantum\Di\Di;

class LoadEnvironmentStageTest extends AppTestCase
{
    public function setUp(): void
    {
        Di::reset();
        $this->context = $this->createContext();

        (new LoadHelpersStage())->process($this->context);
    }

    public function tearDown(): void
    {
        Di::reset();
    }

    public function testLoadEnvironmentStageLoadsEnvVars(): void
    {
        $stage = new LoadEnvironmentStage();
        $stage->process($this->context);

        $this->assertNotEmpty(env('APP_KEY'));
    }

    public function testLoadEnvironmentStageSetsMutableForConsole(): void
    {
        Di::reset();
        $consoleContext = $this->createContext(AppType::CONSOLE);

        (new LoadHelpersStage())->process($consoleContext);

        $stage = new LoadEnvironmentStage();
        $stage->process($consoleContext);

        $this->assertNotEmpty(env('APP_KEY'));
    }
}
