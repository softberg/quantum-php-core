<?php

namespace Quantum\Tests\Unit\App;

use Quantum\App\Contracts\BootStageInterface;
use Quantum\App\BootPipeline;
use Quantum\App\AppContext;
use Quantum\App\Enums\AppType;
use Quantum\Di\DiContainer;
use PHPUnit\Framework\TestCase;
use InvalidArgumentException;
use RuntimeException;

class BootPipelineTest extends TestCase
{
    public function testPipelineRunsStagesInOrder(): void
    {
        $log = [];

        $stage1 = $this->createStage(function () use (&$log) {
            $log[] = 'first';
        });

        $stage2 = $this->createStage(function () use (&$log) {
            $log[] = 'second';
        });

        $stage3 = $this->createStage(function () use (&$log) {
            $log[] = 'third';
        });

        $pipeline = new BootPipeline([$stage1, $stage2, $stage3]);
        $pipeline->run(new AppContext(AppType::WEB, '', new DiContainer()));

        $this->assertSame(['first', 'second', 'third'], $log);
    }

    public function testEmptyPipelineRunsWithoutError(): void
    {
        $pipeline = new BootPipeline([]);
        $pipeline->run(new AppContext(AppType::WEB, '', new DiContainer()));

        $this->assertTrue(true);
    }

    public function testPipelinePassesContextToStages(): void
    {
        $receivedMode = null;

        $stage = $this->createStage(function (AppContext $context) use (&$receivedMode) {
            $receivedMode = $context->getMode();
        });

        $pipeline = new BootPipeline([$stage]);
        $pipeline->run(new AppContext(AppType::CONSOLE, '', new DiContainer()));

        $this->assertSame(AppType::CONSOLE, $receivedMode);
    }

    public function testPipelinePropagatesException(): void
    {
        $stage = $this->createStage(function () {
            throw new RuntimeException('Stage failed');
        });

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Stage failed');

        $pipeline = new BootPipeline([$stage]);
        $pipeline->run(new AppContext(AppType::WEB, '', new DiContainer()));
    }

    public function testPipelineRejectsInvalidStage(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('All stages must implement');

        new BootPipeline([new \stdClass()]);
    }

    private function createStage(callable $callback): BootStageInterface
    {
        return new class ($callback) implements BootStageInterface {
            private $callback;

            public function __construct(callable $callback)
            {
                $this->callback = $callback;
            }

            public function process(AppContext $context): void
            {
                ($this->callback)($context);
            }
        };
    }
}
