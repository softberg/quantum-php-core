<?php

namespace Quantum\Tests\Unit\App\Stages;

use Quantum\App\Stages\LoadEnvironmentStage;
use Quantum\App\Stages\LoadAppConfigStage;
use Quantum\App\Stages\LoadLanguageStage;
use Quantum\App\Stages\LoadHelpersStage;
use Quantum\Tests\Unit\AppTestCase;
use Quantum\Di\Di;

class LoadLanguageStageTest extends AppTestCase
{
    public function setUp(): void
    {
        Di::reset();
        $context = $this->createContext();

        (new LoadHelpersStage())->process($context);
        (new LoadEnvironmentStage())->process($context);
        (new LoadAppConfigStage())->process($context);
    }

    public function tearDown(): void
    {
        config()->flush();
        Di::reset();
    }

    public function testLoadLanguageStageRunsWithoutError(): void
    {
        $stage = new LoadLanguageStage();
        $stage->process($this->createContext());

        $this->assertTrue(true);
    }
}
