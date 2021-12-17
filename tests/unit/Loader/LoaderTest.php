<?php

namespace Quantum\Tests\Loader;

use PHPUnit\Framework\TestCase;
use Quantum\Libraries\Storage\FileSystem;
use Quantum\Loader\Loader;
use Quantum\Loader\Setup;
use Quantum\Di\Di;
use Quantum\App;

class LoaderTest extends TestCase
{

    private $loader;

    public function setUp(): void
    {
        App::loadCoreFunctions(dirname(__DIR__, 3) . DS . 'src' . DS . 'Helpers');

        App::setBaseDir(dirname(__DIR__) . DS . '_root');

        Di::loadDefinitions();

        $this->loader = new Loader(new FileSystem());
    }

    public function testSetupAndLoad()
    {
        $this->loader->setup(new Setup('config', 'config'));

        $this->assertEquals(base_dir() . DS . 'config' . DS . 'config.php', $this->loader->getFilePath());

        $content = $this->loader->load();

        $this->assertIsArray($content);
    }

}