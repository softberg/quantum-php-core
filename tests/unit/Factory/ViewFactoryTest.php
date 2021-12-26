<?php

namespace Quantum\Tests\Factory;

use PHPUnit\Framework\TestCase;
use Quantum\Factory\ViewFactory;
use Quantum\Mvc\QtView;

class ViewFactoryTest extends TestCase
{
    private $viewFactory;
    
    public function setUp(): void
    {
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
