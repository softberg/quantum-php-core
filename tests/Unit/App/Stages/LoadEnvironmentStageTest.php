<?php

namespace Quantum\Tests\Unit\App\Stages;

use Quantum\App\Stages\LoadEnvironmentStage;
use Quantum\App\Stages\LoadHelpersStage;
use Quantum\Tests\Unit\AppTestCase;

class LoadEnvironmentStageTest extends AppTestCase
{
    public function setUp(): void
    {
        $this->context = $this->createContext();

        (new LoadHelpersStage())->process($this->context);
    }

    public function tearDown(): void
    {
        $this->clearAppContext();
    }

    public function testLoadEnvironmentStageLoadsEnvVars(): void
    {
        $stage = new LoadEnvironmentStage();
        $stage->process($this->context);

        $this->assertNotEmpty(env('APP_KEY'));
    }
}
