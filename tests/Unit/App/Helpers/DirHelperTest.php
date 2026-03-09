<?php

namespace Quantum\Tests\Unit\App\Helpers;

use Quantum\Tests\Unit\AppTestCase;

class DirHelperTest extends AppTestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function testBaseDir(): void
    {
        $expectedBaseDir = dirname(__DIR__, 3) . DS . '_root';
        $this->assertEquals($expectedBaseDir, base_dir());
    }

    public function testLogsDir(): void
    {
        $expected = dirname(__DIR__, 3) . DS . '_root' . DS . 'logs';
        $this->assertEquals($expected, logs_dir());
    }

    public function testFrameworkDir(): void
    {
        $expected = dirname(__DIR__, 3) . DS . '_root' . DS . 'vendor' . DS . 'quantum' . DS . 'framework' . DS . 'src';
        $this->assertEquals($expected, framework_dir());
    }

    public function testModulesDir(): void
    {
        $expected = dirname(__DIR__, 3) . DS . '_root' . DS . 'modules';
        $this->assertEquals($expected, modules_dir());
    }

    public function testPublicDir(): void
    {
        $expected = dirname(__DIR__, 3) . DS . '_root' . DS . 'public';
        $this->assertEquals($expected, public_dir());
    }

    public function testUploadsDir(): void
    {
        $expected = dirname(__DIR__, 3) . DS . '_root' . DS . 'public' . DS . 'uploads';
        $this->assertEquals($expected, uploads_dir());
    }

    public function testAssetsDir(): void
    {
        $expected = dirname(__DIR__, 3) . DS . '_root' . DS . 'public' . DS . 'assets';
        $this->assertEquals($expected, assets_dir());
    }

    public function testHooksDir(): void
    {
        $expected = dirname(__DIR__, 3) . DS . '_root' . DS . 'hooks';
        $this->assertEquals($expected, hooks_dir());
    }
}
