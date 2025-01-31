<?php

namespace Quantum\Tests\Renderer;

use Quantum\Renderer\Contracts\TemplateRendererInterface;
use Quantum\Renderer\Exceptions\RendererException;
use Quantum\Renderer\Adapters\TwigAdapter;
use Quantum\Renderer\Adapters\HtmlAdapter;
use Quantum\Renderer\Renderer;
use Quantum\Tests\AppTestCase;
use Quantum\Router\Router;

class RendererTest extends AppTestCase
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

    public function testRendererGetHtmlAdapter()
    {
        $renderer = new Renderer(new HtmlAdapter());

        $this->assertInstanceOf(HtmlAdapter::class, $renderer->getAdapter());

        $this->assertInstanceOf(TemplateRendererInterface::class, $renderer->getAdapter());
    }

    public function testRendererGetTwigAdapter()
    {
        $renderer = new Renderer(new TwigAdapter());

        $this->assertInstanceOf(TwigAdapter::class, $renderer->getAdapter());

        $this->assertInstanceOf(TemplateRendererInterface::class, $renderer->getAdapter());
    }

    public function testRendererCallingValidMethod()
    {
        $renderer = new Renderer(new HtmlAdapter());

        $output = $renderer->render('index', ['name' => 'Tester']);

        $this->assertIsString($output);

        $this->assertSame('<p>Hello Tester, this is rendered html view</p>', $output);
    }

    public function testRendererCallingInvalidMethod()
    {
        $renderer = new Renderer(new HtmlAdapter());

        $this->expectException(RendererException::class);

        $this->expectExceptionMessage('The method `callingInvalidMethod` is not supported for `' . HtmlAdapter::class . '`');

        $renderer->callingInvalidMethod();

    }
}