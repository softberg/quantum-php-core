<?php

namespace Quantum\Tests\Loader;

use Quantum\Libraries\Storage\FileSystem;
use Quantum\Tests\AppTestCase;
use Quantum\Loader\Loader;
use Quantum\Loader\Setup;

class LoaderTest extends AppTestCase
{

    private $loader;

    public function setUp(): void
    {
        parent::setUp();

        $this->loader = new Loader(new FileSystem());
    }

    public function testSetupAndLoad()
    {
        $this->loader->setup(new Setup('config', 'config'));

        $content = $this->loader->load();

        $this->assertEquals(base_dir() . DS . 'shared' . DS . 'config' . DS . 'config.php', $this->loader->getFilePath());

        $this->assertIsArray($content);
    }

}
