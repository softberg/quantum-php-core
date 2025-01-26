<?php

namespace Quantum\Tests\Renderer\Adapters;

use Quantum\Renderer\Adapters\TwigAdapter;
use Quantum\Tests\AppTestCase;
use Quantum\Router\Router;

class TwigAdapterTest extends AppTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Router::setCurrentRoute([
            "route" => "test",
            "method" => "GET",
            "controller" => "SomeController",
            "action" => "test",
            "module" => "Test"
        ]);
    }

    public function testHtmlAdapterRenderView(): void
    {
        $adapter = new TwigAdapter();

        $output = $adapter->render('index.twig', ['name' => 'Tester']);

        $this->assertIsString($output);

        $this->assertSame('<p>Hello Tester, this is rendered twig view</p>', $output);
    }

}