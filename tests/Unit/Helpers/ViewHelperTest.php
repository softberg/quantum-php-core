<?php

namespace Quantum\Tests\Unit\Helpers;

use Quantum\Router\RouteController;
use Quantum\Tests\Unit\AppTestCase;
use Quantum\Factory\ViewFactory;
use Quantum\Di\Di;

class ViewHelperTest extends AppTestCase
{

    public function setUp(): void
    {
        parent::setUp();
    }
    public function tearDown(): void
    {
        $viewFactory = Di::get(ViewFactory::class);

        $viewFactory->setLayout(null);
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

        $this->assertEquals('<p>Hello World, this is rendered html view</p>', view());
    }

    public function testPartial()
    {
        $this->assertEquals('<p>Hello World, this is rendered partial html view</p>', partial('partial'));

        $this->assertEquals('<p>Hello John, this is rendered partial html view</p>', partial('partial', ['name' => 'John']));
    }
}