<?php

namespace Quantum\Tests\Unit\App\Stages;

use Quantum\App\Stages\LoadEnvironmentStage;
use Quantum\App\Stages\LoadAppConfigStage;
use Quantum\App\Stages\LoadHelpersStage;
use Quantum\Tests\Unit\AppTestCase;
use Quantum\App\AppContext;
use Quantum\Di\Di;

class LoadAppConfigStageTest extends AppTestCase
{
    private AppContext $context;

    public function setUp(): void
    {
        Di::reset();
        $this->context = $this->createContext();

        (new LoadHelpersStage())->process($this->context);
        (new LoadEnvironmentStage())->process($this->context);
    }

    public function tearDown(): void
    {
        config()->flush();
        Di::reset();
    }

    public function testLoadAppConfigStageImportsAppConfig(): void
    {
        $this->assertFalse(config()->has('app'));

        $stage = new LoadAppConfigStage();
        $stage->process($this->context);

        $this->assertTrue(config()->has('app'));
    }

    public function testLoadAppConfigStageSkipsIfAlreadyLoaded(): void
    {
        $stage = new LoadAppConfigStage();

        $stage->process($this->context);

        $this->assertTrue(config()->has('app'));

        $stage->process($this->context);

        $this->assertTrue(config()->has('app'));
    }
}
