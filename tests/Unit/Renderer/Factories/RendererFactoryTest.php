<?php

namespace Quantum\Tests\Unit\Renderer\Factories;

use Quantum\Renderer\Contracts\TemplateRendererInterface;
use Quantum\Renderer\Exceptions\RendererException;
use Quantum\Renderer\Factories\RendererFactory;
use Quantum\Renderer\Adapters\HtmlAdapter;
use Quantum\Renderer\Adapters\TwigAdapter;
use Quantum\Tests\Unit\AppTestCase;
use Quantum\Renderer\Renderer;

class RendererFactoryTest extends AppTestCase
{

    public function setUp(): void
    {
        parent::setUp();

        $this->setPrivateProperty(RendererFactory::class, 'instances', []);
    }

    public function testRendererFactoryInstance()
    {
        $renderer = RendererFactory::get();

        $this->assertInstanceOf(Renderer::class, $renderer);
    }

    public function testRendererFactoryGetDefaultAdapter()
    {
        $renderer = RendererFactory::get();

        $this->assertInstanceOf(TemplateRendererInterface::class, $renderer->getAdapter());

        $this->assertInstanceOf(HtmlAdapter::class, $renderer->getAdapter());
    }

    public function testRendererFactoryGetHtmlAdapter()
    {
        $renderer = RendererFactory::get(Renderer::HTML);

        $this->assertInstanceOf(TemplateRendererInterface::class, $renderer->getAdapter());

        $this->assertInstanceOf(HtmlAdapter::class, $renderer->getAdapter());
    }

    public function testRendererFactoryTwigAdapter()
    {
        $renderer = RendererFactory::get(Renderer::TWIG);

        $this->assertInstanceOf(TemplateRendererInterface::class, $renderer->getAdapter());

        $this->assertInstanceOf(TwigAdapter::class, $renderer->getAdapter());
    }

    public function testRendererFactoryInvalidTypeAdapter()
    {
        $this->expectException(RendererException::class);

        $this->expectExceptionMessage('The adapter `invalid_type` is not supported`');

        RendererFactory::get('invalid_type');
    }

    public function testRendererFactoryReturnsSameInstance()
    {
        $renderer1 = RendererFactory::get(Renderer::HTML);
        $renderer2 = RendererFactory::get(Renderer::HTML);

        $this->assertSame($renderer1, $renderer2);
    }
}