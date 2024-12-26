<?php

namespace Quantum\Tests\Debugger;

use DebugBar\DataCollector\MessagesCollector;
use DebugBar\DataCollector\PhpInfoCollector;
use Quantum\Debugger\DebuggerStore;
use DebugBar\JavascriptRenderer;
use Quantum\Debugger\Debugger;
use Quantum\Tests\AppTestCase;
use DebugBar\DebugBar;
use Psr\Log\LogLevel;
use ReflectionClass;
use Mockery;

class DebuggerTest extends AppTestCase
{
    private $debugger;
    private $debugBarMock;
    private $debuggerStore;

    public function setUp(): void
    {
        parent::setUp();

        $this->debuggerStore = new DebuggerStore();

        $this->debugBarMock = Mockery::mock(DebugBar::class);

        $collectors = [
            Mockery::mock(PhpInfoCollector::class),
            Mockery::mock(MessagesCollector::class),
        ];

        foreach ($collectors as $collector) {
            $this->debugBarMock->shouldReceive('addCollector')
                ->once()
                ->with($collector);
        }

        $reflection = new ReflectionClass(Debugger::class);
        $instanceProperty = $reflection->getProperty('instance');
        $instanceProperty->setAccessible(true);
        $instanceProperty->setValue(null);

        $this->debugger = Debugger::getInstance($this->debuggerStore, $this->debugBarMock, $collectors);
    }

    public function testDebuggerIsEnabled()
    {
        config()->set('debug', true);
        $this->assertTrue($this->debugger->isEnabled());

        config()->set('debug', false);
        $this->assertFalse($this->debugger->isEnabled());
    }

    public function testDebuggerInitStore()
    {
        $this->debugger->initStore();

        $this->assertTrue($this->debuggerStore->has(Debugger::MESSAGES));
        $this->assertTrue($this->debuggerStore->has(Debugger::QUERIES));
        $this->assertTrue($this->debuggerStore->has(Debugger::ROUTES));
        $this->assertTrue($this->debuggerStore->has(Debugger::HOOKS));
        $this->assertTrue($this->debuggerStore->has(Debugger::MAILS));
    }

    public function testDebuggerAddToStoreCell()
    {
        $this->debugger->addToStoreCell(Debugger::MESSAGES, LogLevel::INFO, 'Test message');

        $storedData = $this->debuggerStore->get(Debugger::MESSAGES);

        $this->assertEquals(['info' => 'Test message'], $storedData[0]);
    }

    public function testDebuggerGetStoreCell()
    {
        $this->debugger->addToStoreCell(Debugger::MESSAGES, LogLevel::INFO, 'Test message');

        $storedData = $this->debugger->getStoreCell(Debugger::MESSAGES);

        $this->assertEquals(['info' => 'Test message'], $storedData[0]);
    }

    public function testDebuggerClearStoreCell()
    {
        $this->debugger->addToStoreCell(Debugger::MESSAGES, LogLevel::INFO, 'Test message');
        $this->debugger->clearStoreCell(Debugger::MESSAGES);

        $storedData = $this->debuggerStore->get(Debugger::MESSAGES);

        $this->assertEmpty($storedData);
    }

    public function testDebuggerResetStore()
    {
        $this->debugger->initStore();

        $this->assertNotEmpty($this->debuggerStore->all());

        $this->debugger->resetStore();

        $this->assertEmpty($this->debuggerStore->all());
    }

    public function testDebugbarRender()
    {
        $this->debugger->initStore();

        $rendererMock = Mockery::mock('alias:' . JavascriptRenderer::class);
        $rendererMock->shouldReceive('setBaseUrl')->andReturnSelf();
        $rendererMock->shouldReceive('addAssets')->andReturnSelf();
        $rendererMock->shouldReceive('renderHead')->andReturn('<head></head>');
        $rendererMock->shouldReceive('render')->andReturn('<div>Debug Bar</div>');

        $this->debugBarMock->shouldReceive('getJavascriptRenderer')->andReturn($rendererMock);

        $this->debugBarMock->shouldReceive('hasCollector')->with(Mockery::any())->andReturn(true);

        $output = $this->debugger->render();

        $this->assertStringContainsString('<head></head>', $output);
        $this->assertStringContainsString('<div>Debug Bar</div>', $output);
    }
}
