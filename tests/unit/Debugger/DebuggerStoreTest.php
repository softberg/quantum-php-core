<?php

namespace Quantum\Tests\Debugger;

use Quantum\Debugger\DebuggerStore;
use Quantum\Tests\AppTestCase;

class DebuggerStoreTest extends AppTestCase
{

    private $debuggerStore;

    public function setUp(): void
    {
        $this->debuggerStore = new DebuggerStore();
    }

    public function tearDown(): void
    {
        $this->debuggerStore->flush();
    }

    public function testDebuggerStoreInit()
    {
        $this->debuggerStore->init(['key1', 'key2']);

        $this->assertTrue($this->debuggerStore->has('key1'));
        $this->assertTrue($this->debuggerStore->has('key2'));
        $this->assertEmpty($this->debuggerStore->get('key1'));
        $this->assertEmpty($this->debuggerStore->get('key2'));
    }

    public function testDebuggerStoreAll()
    {
        $this->assertEquals([], $this->debuggerStore->all());

        $this->debuggerStore->init(['key1']);
        $this->debuggerStore->set('key1', ['level1' => 'data']);

        $this->assertEquals(['key1' => [['level1' => 'data']]], $this->debuggerStore->all());

        $all = $this->debuggerStore->all();
        $this->assertArrayHasKey('key1', $all);
    }

    public function testDebuggerStoreHas()
    {
        $this->debuggerStore->init(['key1']);

        $this->assertTrue($this->debuggerStore->has('key1'));
        $this->assertFalse($this->debuggerStore->has('key2'));
    }

    public function testDebuggerStoreSetAndGet()
    {
        $this->debuggerStore->init(['key1']);
        $this->debuggerStore->set('key1', ['level1' => 'data']);

        $this->assertEquals([['level1' => 'data']], $this->debuggerStore->get('key1'));
        $this->assertCount(1, $this->debuggerStore->get('key1'));


        $this->debuggerStore->set('key1', ['level2' => 'more data']);

        $this->assertEquals([['level1' => 'data'], ['level2' => 'more data']], $this->debuggerStore->get('key1'));
        $this->assertCount(2, $this->debuggerStore->get('key1'));
    }

    public function testDebuggerStoreDelete()
    {
        $this->debuggerStore->init(['key1']);
        $this->debuggerStore->set('key1', ['level1' => 'data']);

        $this->assertTrue($this->debuggerStore->has('key1'));

        $this->debuggerStore->delete('key1');
        $this->assertEmpty($this->debuggerStore->get('key1'));
    }

    public function testFlush()
    {
        $debuggerStore = new DebuggerStore();
        $debuggerStore->init(['key1', 'key2']);
        $debuggerStore->set('key1', ['level1' => 'data']);
        $debuggerStore->set('key2', ['level2' => 'more data']);

        $debuggerStore->flush();

        $this->assertEmpty($debuggerStore->get('key1'));
        $this->assertEmpty($debuggerStore->get('key2'));
    }
}

