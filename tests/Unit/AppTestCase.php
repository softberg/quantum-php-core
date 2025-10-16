<?php

namespace Quantum\Tests\Unit;

use Quantum\Libraries\Storage\Factories\FileSystemFactory;
use Quantum\App\Factories\AppFactory;
use Quantum\Environment\Environment;
use PHPUnit\Framework\TestCase;
use Quantum\Config\Config;
use Quantum\Loader\Setup;
use ReflectionClass;
use Quantum\App\App;
use Quantum\Di\Di;

abstract class AppTestCase extends TestCase
{

    protected $fs;

    public function setUp(): void
    {
        if (!file_exists(PROJECT_ROOT . DS . '.env.testing')) {
            createEnvFile();
        }

        AppFactory::create(App::WEB, PROJECT_ROOT);

        config()->flush();

        Environment::getInstance()
            ->setMutable(true)
            ->load(new Setup('config', 'env'));

        config()->import(new Setup('config', 'app'));

//        $coreDependencies = [
//            \Quantum\Loader\Loader::class => \Quantum\Loader\Loader::class,
//            \Quantum\Http\Request::class => \Quantum\Http\Request::class,
//            \Quantum\Http\Response::class => \Quantum\Http\Response::class,
//        ];
//
//        Di::registerDependencies($coreDependencies);

        $this->fs = FileSystemFactory::get();
    }

    public function tearDown(): void
    {
        AppFactory::destroy(App::WEB);
        config()->flush();
        Di::reset();
    }

    protected function setPrivateProperty($object, $property, $value): void
    {
        $reflection = new ReflectionClass($object);
        $property = $reflection->getProperty($property);
        $property->setAccessible(true);
        $property->setValue($value);
    }

    protected function getPrivateProperty($object, $property)
    {
        $reflection = new ReflectionClass($object);
        $property = $reflection->getProperty($property);
        $property->setAccessible(true);
        return $property->getValue($object);
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