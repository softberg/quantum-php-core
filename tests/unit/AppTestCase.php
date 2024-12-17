<?php

namespace Quantum\Tests;

use Quantum\Libraries\Storage\FileSystem;
use Quantum\Libraries\Lang\Lang;
use PHPUnit\Framework\TestCase;
use Quantum\Loader\Setup;
use Quantum\Di\Di;
use Quantum\App;

abstract class AppTestCase extends TestCase
{

    public function setUp(): void
    {
        App::setBaseDir(__DIR__ . DS . '_root');

        App::loadCoreFunctions(dirname(__DIR__, 2) . DS . 'src' . DS . 'Helpers');

        Di::loadDefinitions();

        config()->flush();

        putenv('APP_KEY=' . uniqid());

        config()->load(new Setup('config', 'config', true));

        Lang::getInstance()->setLang('en')->load();
    }

    protected function createFile(string $filePath, string $content)
    {
        $fs = Di::get(FileSystem::class);

        $fs->put($filePath, $content);
    }

    protected function removeFile(string $filePath)
    {
        $fs = Di::get(FileSystem::class);

        if($fs->exists($filePath)) {
            $fs->remove($filePath);
        }
    }

}
