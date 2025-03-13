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

        $this->setPrivateProperty(RendererFactory::class, 'instance', null);
    }

    public function testRendererFactoryInstance()
    {
        $renderer = RendererFactory::get();

        $this->assertInstanceOf(Renderer::class, $renderer);
    }

    public function testRendererFactoryHtmlAdapter()
    {
        config()->set('view.default', 'html');

        $renderer = RendererFactory::get();

        $this->assertInstanceOf(TemplateRendererInterface::class, $renderer->getAdapter());

        $this->assertInstanceOf(HtmlAdapter::class, $renderer->getAdapter());
    }

    public function testRendererFactoryTwigAdapter()
    {
        config()->set('view.default', 'twig');

        $renderer = RendererFactory::get();

        $this->assertInstanceOf(TemplateRendererInterface::class, $renderer->getAdapter());

        $this->assertInstanceOf(TwigAdapter::class, $renderer->getAdapter());
    }

    public function testRendererFactoryInvalidTypeAdapter()
    {
        config()->set('view.default', 'invalid');

        $this->expectException(RendererException::class);

        $this->expectExceptionMessage('The adapter `invalid` is not supported`');

        RendererFactory::get();
    }

    public function testRendererFactoryReturnsSameInstance()
    {
        $renderer1 = RendererFactory::get();
        $renderer2 = RendererFactory::get();

        $this->assertSame($renderer1, $renderer2);
    }
}