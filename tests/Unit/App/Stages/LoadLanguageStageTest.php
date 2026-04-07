<?php

namespace Quantum\Tests\Unit\App\Stages;

use Quantum\App\Stages\LoadEnvironmentStage;
use Quantum\App\Stages\LoadAppConfigStage;
use Quantum\App\Stages\LoadLanguageStage;
use Quantum\App\Stages\LoadHelpersStage;
use PHPUnit\Framework\TestCase;
use Quantum\App\Enums\AppType;
use Quantum\App\AppContext;
use Quantum\App\App;
use Quantum\Di\Di;

class LoadLanguageStageTest extends TestCase
{
    public function setUp(): void
    {
        Di::reset();
        App::setBaseDir(PROJECT_ROOT);

        $context = new AppContext(AppType::WEB);

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
        $stage->process(new AppContext(AppType::WEB));

        $this->assertTrue(true);
    }
}
