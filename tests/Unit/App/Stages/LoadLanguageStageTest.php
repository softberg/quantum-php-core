<?php

namespace Quantum\Tests\Unit\App\Stages;

use Quantum\App\Stages\LoadEnvironmentStage;
use Quantum\App\Stages\LoadAppConfigStage;
use Quantum\App\Stages\LoadLanguageStage;
use Quantum\App\Stages\LoadHelpersStage;
use Quantum\Tests\Unit\AppTestCase;
use Quantum\App\AppContext;
use Quantum\Di\Di;

class LoadLanguageStageTest extends AppTestCase
{
    private AppContext $context;

    public function setUp(): void
    {
        Di::reset();
        $this->context = $this->createContext();

        (new LoadHelpersStage())->process($this->context);
        (new LoadEnvironmentStage())->process($this->context);
        (new LoadAppConfigStage())->process($this->context);
    }

    public function tearDown(): void
    {
        config()->flush();
        Di::reset();
    }

    public function testLoadLanguageStageRunsWithoutError(): void
    {
        $stage = new LoadLanguageStage();
        $stage->process($this->context);

        $this->assertTrue(true);
    }
}
