<?php

namespace Quantum\Tests\Unit\View\Helpers;

use Quantum\View\Factories\ViewFactory;
use Quantum\Router\MatchedRoute;
use Quantum\Router\Route;
use Quantum\Http\Request;
use Quantum\Di\Di;
use Quantum\Tests\Unit\AppTestCase;

class ViewHelperTest extends AppTestCase
{
    private $view;

    public function setUp(): void
    {
        parent::setUp();

        $route = new Route(
            ['GET', 'POST'],
            '/test',
            'TestController',
            'testAction',
            null
        );
        $route->module('Test');

        $matchedRoute = new MatchedRoute($route, []);
        Request::setMatchedRoute($matchedRoute);

        $request = Di::get(Request::class);
        $request->create('POST', '/test');
        Request::setMatchedRoute($matchedRoute);

        $this->view = ViewFactory::get();
    }
    public function tearDown(): void
    {
        $this->view->setLayout(null);
    }

    public function testView()
    {
        $this->view->setLayout('layout');

        $this->view->render('index');

        $this->assertEquals('<p>Hello World, this is rendered html view</p>', view());
    }

    public function testPartial()
    {
        $this->assertEquals('<p>Hello World, this is rendered partial html view</p>', partial('partial'));

        $this->assertEquals('<p>Hello John, this is rendered partial html view</p>', partial('partial', ['name' => 'John']));
    }

    public function testMarkdownToHtml(): void
    {
        $markdown = '**bold** text';
        $html = trim(markdown_to_html($markdown, true));

        $this->assertEquals('<p><strong>bold</strong> text</p>', $html);
    }
}
