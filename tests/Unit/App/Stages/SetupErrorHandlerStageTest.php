<?php

namespace Quantum\Tests\Unit\App\Stages;

use Quantum\App\Stages\SetupErrorHandlerStage;
use Quantum\App\Stages\LoadEnvironmentStage;
use Quantum\App\Stages\LoadAppConfigStage;
use Quantum\App\Stages\LoadHelpersStage;
use Quantum\Tests\Unit\AppTestCase;
use Quantum\Di\Di;

class SetupErrorHandlerStageTest extends AppTestCase
{
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
        restore_error_handler();
        restore_exception_handler();
        config()->flush();
        Di::reset();
    }

    public function testSetupErrorHandlerStageRegistersHandlers(): void
    {
        $stage = new SetupErrorHandlerStage();
        $stage->process($this->context);

        $errorHandler = set_error_handler(function () {
        });
        restore_error_handler();

        $this->assertNotNull($errorHandler);
    }
}
