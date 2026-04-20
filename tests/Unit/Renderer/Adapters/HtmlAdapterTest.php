<?php

namespace Quantum\Tests\Unit\Renderer\Adapters;

use Quantum\Renderer\Adapters\HtmlAdapter;
use Quantum\Tests\Unit\AppTestCase;
use Quantum\Router\MatchedRoute;
use Quantum\Router\Route;

class HtmlAdapterTest extends AppTestCase
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
        $adapter = new HtmlAdapter();

        $output = $adapter->render('index', ['name' => 'Tester']);

        $this->assertIsString($output);

        $this->assertSame('<p>Hello Tester, this is rendered html view</p>', $output);
    }

}
