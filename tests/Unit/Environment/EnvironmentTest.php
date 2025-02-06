<?php

namespace Quantum\Tests\Unit\Environment;

use Quantum\Environment\Environment;
use Quantum\Tests\Unit\AppTestCase;
use Quantum\App\App;

class EnvironmentTest extends AppTestCase
{
    private $env;

    public function setUp(): void
    {
        parent::setUp();

        $this->env = Environment::getInstance();
    }

    public function testEnvironmentGetAppEnv()
    {
        $this->assertEquals('testing', $this->env->getAppEnv());
    }

    public function testEnvironmentGetValue()
    {
        $this->assertNull($this->env->getValue('NON_EXISTING_KEY'));

        $this->assertNotNull($this->env->getValue('APP_KEY'));

        $this->assertEquals('TRUE', $this->env->getValue('DEBUG'));

        $this->assertEquals('XYZ1234567890', $this->env->getValue('APP_KEY'));
    }

    public function testEnvironmentHasKey()
    {
        $this->assertTrue($this->env->hasKey('APP_KEY'));

        $this->assertFalse($this->env->hasKey('NON_EXISTING_KEY'));
    }

    public function testEnvironmentGetRow()
    {
        $this->assertNull($this->env->getRow('NON_EXISTING_KEY'));

        $this->assertEquals('APP_KEY=XYZ1234567890', $this->env->getRow('APP_KEY'));
    }

    public function testEnvironmentAddAndUpdateRow()
    {
        $this->assertNull($this->env->getValue('SOMETHING'));

        $this->env->updateRow('SOMETHING', 'Something');

        $this->assertEquals('Something', $this->env->getValue('SOMETHING'));

        $this->env->updateRow('SOMETHING', 'Something_else');

        $this->assertEquals('Something_else', $this->env->getValue('SOMETHING'));

        $this->fs->remove(App::getBaseDir() . DS . '.env.testing');
    }
}