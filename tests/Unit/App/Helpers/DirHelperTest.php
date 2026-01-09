<?php

namespace Quantum\Tests\Unit\App\Helpers;

use Quantum\Tests\Unit\AppTestCase;

class DirHelperTest extends AppTestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function testBaseDir()
    {
        $expectedBaseDir = dirname(__DIR__, 3) . DS . '_root';
        $this->assertEquals($expectedBaseDir, base_dir());
    }

    public function testLogsDir()
    {
        $expected = dirname(__DIR__, 3) . DS . '_root' . DS . 'logs';
        $this->assertEquals($expected, logs_dir());
    }

    public function testFrameworkDir()
    {
        $expected = dirname(__DIR__, 3) . DS . '_root' . DS . 'vendor' . DS . 'quantum' . DS . 'framework' . DS . 'src';
        $this->assertEquals($expected, framework_dir());
    }

    public function testModulesDir()
    {
        $expected = dirname(__DIR__, 3) . DS . '_root' . DS . 'modules';
        $this->assertEquals($expected, modules_dir());
    }

    public function testPublicDir()
    {
        $expected = dirname(__DIR__, 3) . DS . '_root' . DS . 'public';
        $this->assertEquals($expected, public_dir());
    }

    public function testUploadsDir()
    {
        $expected = dirname(__DIR__, 3) . DS . '_root' . DS . 'public' . DS . 'uploads';
        $this->assertEquals($expected, uploads_dir());
    }

    public function testAssetsDir()
    {
        $expected = dirname(__DIR__, 3) . DS . '_root' . DS . 'public' . DS . 'assets';
        $this->assertEquals($expected, assets_dir());
    }

    public function testHooksDir()
    {
        $expected = dirname(__DIR__, 3) . DS . '_root' . DS . 'hooks';
        $this->assertEquals($expected, hooks_dir());
    }
}
