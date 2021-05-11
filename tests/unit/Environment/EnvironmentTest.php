<?php

namespace Quantum\Test\Unit;

use PHPUnit\Framework\TestCase;
use Quantum\Environment\Environment;
use Quantum\Libraries\Storage\FileSystem;
use Quantum\Loader\Loader;
use Mockery;

class EnvironmentTest extends TestCase
{

    private $env;
    private $loaderMock;

    public function setUp(): void
    {
        $this->loaderMock = Mockery::mock('Quantum\Loader\Loader');

        $this->loaderMock->shouldReceive('setup')->andReturn($this->loaderMock);

        $this->loaderMock->shouldReceive('load')->andReturn([
            'app_env' => 'staging'
        ]);

        $fs = new FileSystem();

        $loader = new Loader($fs);

        $loader->loadDir(dirname(__DIR__, 3) . DS . 'src' . DS . 'Helpers' . DS . 'functions');

        $loader->loadFile(dirname(__DIR__, 3) . DS . 'src' . DS . 'constants.php');

        $fs->put(base_dir() . DS . '.env.staging', "DEBUG=TRUE\nAPP_KEY=stg_123456\n");

        $this->env = Environment::getInstance();
    }

    public function testEnvLoadAndGetValue()
    {
        $this->assertNull($this->env->getValue('APP_KEY'));

        $this->env->load($this->loaderMock);

        $this->assertNotNull($this->env->getValue('APP_KEY'));

        $this->assertEquals('stg_123456', $this->env->getValue('APP_KEY'));

        $this->assertEquals('TRUE', $this->env->getValue('DEBUG'));
    }

    public function testEnvUpdateRow()
    {
        $this->env->load($this->loaderMock);

        $this->assertEquals('stg_123456', $this->env->getValue('APP_KEY'));

        $this->env->updateRow('APP_KEY', 'stg_456789');

        $this->assertEquals('stg_456789', $this->env->getValue('APP_KEY'));
    }

}
