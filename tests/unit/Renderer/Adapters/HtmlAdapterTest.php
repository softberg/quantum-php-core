<?php

namespace Quantum\Tests\Unit\Renderer\Adapters;

use Quantum\Renderer\Adapters\HtmlAdapter;
use Quantum\Tests\Unit\AppTestCase;
use Quantum\Router\Router;

class HtmlAdapterTest extends AppTestCase
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
        $adapter = new HtmlAdapter();

        $output = $adapter->render('index', ['name' => 'Tester']);

        $this->assertIsString($output);

        $this->assertSame('<p>Hello Tester, this is rendered html view</p>', $output);
    }

}