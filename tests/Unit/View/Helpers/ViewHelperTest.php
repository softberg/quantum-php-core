<?php

namespace Quantum\Tests\Unit\View\Helpers;

use Quantum\View\Factories\ViewFactory;
use Quantum\Router\RouteController;
use Quantum\Tests\Unit\AppTestCase;

class ViewHelperTest extends AppTestCase
{

    private $view;

    public function setUp(): void
    {
        parent::setUp();

        $this->view = ViewFactory::get();
    }
    public function tearDown(): void
    {
        $this->view->setLayout(null);
    }

    public function testView()
    {
        RouteController::setCurrentRoute([
            "route" => "test",
            "method" => "POST",
            "controller" => "TestController",
            "action" => "testAction",
            "module" => "Test",
        ]);

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
        $markdown = "**bold** text";
        $html = trim(markdown_to_html($markdown, true));

        $this->assertEquals('<p><strong>bold</strong> text</p>', $html);
    }
}