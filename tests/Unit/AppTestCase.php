<?php

namespace Quantum\Tests\Unit;

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
        AppFactory::create(App::WEB, PROJECT_ROOT);

        Config::getInstance()->flush();

        Environment::getInstance()
            ->setMutable(true)
            ->load(new Setup('config', 'env'));

        Config::getInstance()
            ->load(new Setup('config', 'config'));

        $this->fs = FileSystemFactory::get();
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