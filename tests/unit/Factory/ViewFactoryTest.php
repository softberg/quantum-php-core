<?php

namespace Quantum\Tests\Unit\Factory;

use Quantum\Tests\Unit\AppTestCase;
use Quantum\Factory\ViewFactory;
use Quantum\Mvc\QtView;

class ViewFactoryTest extends AppTestCase
{

    private $viewFactory;

    public function setUp(): void
    {
        parent::setUp();
        
        $this->viewFactory = new ViewFactory();
    }

    public function testGetInstance()
    {
        $this->assertInstanceOf(QtView::class, $this->viewFactory->getInstance());
    }

    public function testProxyCalls()
    {
        $view = $this->viewFactory->getInstance();

        $view->setParam('key', 'Value');

        $this->assertEquals('Value', $view->getParam('key'));
    }

}
