<?php

namespace Quantum\Tests\Unit\App\Stages;

use Quantum\App\Stages\LoadEnvironmentStage;
use Quantum\App\Stages\LoadAppConfigStage;
use Quantum\App\Stages\LoadHelpersStage;
use PHPUnit\Framework\TestCase;
use Quantum\App\Enums\AppType;
use Quantum\App\AppContext;
use Quantum\App\App;
use Quantum\Di\Di;

class LoadAppConfigStageTest extends TestCase
{
    public function setUp(): void
    {
        Di::reset();
        App::setBaseDir(PROJECT_ROOT);

        $context = new AppContext(AppType::WEB);

        (new LoadHelpersStage())->process($context);
        (new LoadEnvironmentStage())->process($context);
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
        $stage->process(new AppContext(AppType::WEB));

        $this->assertTrue(config()->has('app'));
    }

    public function testLoadAppConfigStageSkipsIfAlreadyLoaded(): void
    {
        $stage = new LoadAppConfigStage();
        $context = new AppContext(AppType::WEB);

        $stage->process($context);

        $this->assertTrue(config()->has('app'));

        $stage->process($context);

        $this->assertTrue(config()->has('app'));
    }
}
