<?php

namespace Quantum\Tests\Unit\Renderer\Adapters;

use Quantum\Renderer\Adapters\TwigAdapter;
use Quantum\Tests\Unit\AppTestCase;
use Quantum\Router\MatchedRoute;
use Quantum\Router\Route;

class TwigAdapterTest extends AppTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $route = new Route(
            ['GET'],
            '/test',
            'SomeController',
            'test'
        );
        $route->module('Test');

        $matchedRoute = new MatchedRoute($route, []);

        request()->create('GET', '/test');
        request()->setMatchedRoute($matchedRoute);
    }

    public function testHtmlAdapterRenderView(): void
    {
        $adapter = new TwigAdapter();

        $output = $adapter->render('index.twig', ['name' => 'Tester']);

        $this->assertIsString($output);

        $this->assertSame('<p>Hello Tester, this is rendered twig view</p>', $output);
    }

}
