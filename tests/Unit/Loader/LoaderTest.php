<?php

namespace Quantum\Tests\Unit\Loader;

use Quantum\Tests\Unit\AppTestCase;
use Quantum\Loader\Loader;
use Quantum\Loader\Setup;

class LoaderTest extends AppTestCase
{

    private $loader;

    public function setUp(): void
    {
        parent::setUp();

        $this->loader = new Loader();
    }

    public function testSetupAndLoad()
    {
        $this->loader->setup(new Setup('config', 'app'));

        $content = $this->loader->load();

        $this->assertEquals(base_dir() . DS . 'shared' . DS . 'config' . DS . 'app.php', $this->loader->getFilePath());

        $this->assertIsArray($content);
    }

}