<?php

namespace Quantum\Tests\Helpers;

use Quantum\Debugger\DebuggerStore;
use Quantum\Debugger\Debugger;
use Quantum\Tests\AppTestCase;

class VarHelperTest extends AppTestCase
{

    private $debugger;

    public function setUp(): void
    {
        parent::setUp();

        $store = new DebuggerStore();

        $this->debugger = Debugger::getInstance($store);

        $this->debugger->initStore();
    }

    public function tearDown(): void
    {
        $this->debugger->resetStore();
    }

    public function testErrorHelper()
    {
        error('Fatal Error');

        $storedMessages = $this->debugger->getStoreCell(Debugger::MESSAGES);

        $this->assertArrayHasKey('error', $storedMessages[0]);

        $this->assertEquals('Fatal Error', $storedMessages[0]['error']);
    }

    public function testWarningHelper()
    {
        warning('Warning!!');

        $storedMessages = $this->debugger->getStoreCell(Debugger::MESSAGES);

        $this->assertArrayHasKey('warning', $storedMessages[0]);

        $this->assertEquals('Warning!!', $storedMessages[0]['warning']);
    }

    public function testNoticeHelper()
    {
        notice('Simple Notice');

        $storedMessages = $this->debugger->getStoreCell(Debugger::MESSAGES);

        $this->assertArrayHasKey('notice', $storedMessages[0]);

        $this->assertEquals('Simple Notice', $storedMessages[0]['notice']);
    }

    public function testInfoHelper()
    {
        info('For your information');

        $storedMessages = $this->debugger->getStoreCell(Debugger::MESSAGES);

        $this->assertArrayHasKey('info', $storedMessages[0]);

        $this->assertEquals('For your information', $storedMessages[0]['info']);
    }

    public function testDebugHelper()
    {
        debug('Debugging!!');

        $storedMessages = $this->debugger->getStoreCell(Debugger::MESSAGES);

        $this->assertArrayHasKey('debug', $storedMessages[0]);

        $this->assertEquals('Debugging!!', $storedMessages[0]['debug']);
    }
}