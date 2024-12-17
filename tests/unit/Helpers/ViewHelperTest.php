<?php

namespace Quantum\Tests\Helpers;

use Quantum\Di\Di;
use Quantum\Factory\ViewFactory;
use Quantum\Router\RouteController;
use Quantum\Tests\AppTestCase;

class ViewHelperTest extends AppTestCase
{

    public function setUp(): void
    {
        parent::setUp();
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

        $viewFactory = Di::get(ViewFactory::class);

        $viewFactory->setLayout('layout');

        $viewFactory->render('index');

        $this->assertEquals('<p>Hello World, this is rendered view</p>', view());

        $viewFactory->setLayout(null);
    }

    public function testPartial()
    {
        $this->assertEquals('<p>Hello World, this is rendered partial view</p>', partial('partial'));

        $this->assertEquals('<p>Hello John, this is rendered partial view</p>', partial('partial', ['name' => 'John']));
    }
}