<?php

namespace Quantum\Tests\Renderer\Factories;

use Quantum\Renderer\Contracts\TemplateRendererInterface;
use Quantum\Renderer\Exceptions\RendererException;
use Quantum\Renderer\Factories\RendererFactory;
use Quantum\Renderer\Adapters\HtmlAdapter;
use Quantum\Renderer\Adapters\TwigAdapter;
use Quantum\Renderer\Renderer;
use Quantum\Tests\AppTestCase;
use ReflectionClass;

class RendererFactoryTest extends AppTestCase
{

    public function setUp(): void
    {
        parent::setUp();

        $reflection = new ReflectionClass(RendererFactory::class);
        $property = $reflection->getProperty('instance');
        $property->setAccessible(true);
        $property->setValue(null);
    }

    public function testRendererFactoryInstance()
    {
        $renderer = RendererFactory::get();

        $this->assertInstanceOf(Renderer::class, $renderer);
    }

    public function testRendererFactoryHtmlAdapter()
    {
        config()->set('view.current', 'html');

        $renderer = RendererFactory::get();

        $this->assertInstanceOf(TemplateRendererInterface::class, $renderer->getAdapter());

        $this->assertInstanceOf(HtmlAdapter::class, $renderer->getAdapter());
    }

    public function testRendererFactoryTwigAdapter()
    {
        config()->set('view.current', 'twig');

        $renderer = RendererFactory::get();

        $this->assertInstanceOf(TemplateRendererInterface::class, $renderer->getAdapter());

        $this->assertInstanceOf(TwigAdapter::class, $renderer->getAdapter());
    }

    public function testRendererFactoryInvalidTypeAdapter()
    {
        config()->set('view.current', 'invalid');

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