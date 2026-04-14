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
        $context = $this->createContext();

        (new LoadHelpersStage())->process($context);
    }

    public function tearDown(): void
    {
        Di::reset();
    }

    public function testLoadEnvironmentStageLoadsEnvVars(): void
    {
        $stage = new LoadEnvironmentStage();
        $stage->process($this->createContext());

        $this->assertNotEmpty(env('APP_KEY'));
    }

    public function testLoadEnvironmentStageSetsMutableForConsole(): void
    {
        $stage = new LoadEnvironmentStage();
        $stage->process($this->createContext(AppType::CONSOLE));

        $this->assertNotEmpty(env('APP_KEY'));
    }
}
