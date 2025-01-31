<?php

namespace Quantum\Tests;

use Quantum\Libraries\Storage\Factories\FileSystemFactory;
use Quantum\App\Factories\AppFactory;
use Quantum\Environment\Environment;
use Quantum\Libraries\Config\Config;
use PHPUnit\Framework\TestCase;
use Quantum\Loader\Setup;
use Quantum\App\App;
use ReflectionClass;

abstract class AppTestCase extends TestCase
{

    protected $fs;

    public function setUp(): void
    {
        $this->fs = FileSystemFactory::get();

        AppFactory::create(App::WEB, __DIR__ . DS . '_root');

        Config::getInstance()->flush();

        if (!$this->fs->exists(App::getBaseDir() . DS . '.env.testing')) {
            $this->fs->copy(
                App::getBaseDir() . DS . '.env.example',
                App::getBaseDir() . DS . '.env.testing'
            );
        }

        Environment::getInstance()
            ->setMutable(true)
            ->load(new Setup('config', 'env'));

        Config::getInstance()->load(new Setup('config', 'config', true));
    }

    public function tearDown(): void
    {
        if ($this->fs->exists(App::getBaseDir() . DS . '.env.testing')) {
            $this->fs->remove(App::getBaseDir() . DS . '.env.testing');
        }
    }

    protected function setPrivateProperty($object, $property, $value): void
    {
        $reflection = new ReflectionClass($object);
        $property = $reflection->getProperty($property);
        $property->setAccessible(true);
        $property->setValue($value);
    }

    protected function createFile(string $filePath, string $content)
    {
        $this->fs->put($filePath, $content);
    }

    protected function removeFile(string $filePath)
    {
        if ($this->fs->exists($filePath)) {
            $this->fs->remove($filePath);
        }
    }
}