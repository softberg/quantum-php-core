<?php

namespace Quantum\Test\Unit;

use PHPUnit\Framework\TestCase;
use Quantum\Factory\ViewFactory;

class ViewFactoryTest extends TestCase
{
    private $viewFactory;
    
    public function setUp(): void
    {
        $this->viewFactory = new ViewFactory();
    }

    public function testGetInstance()
    {
        $this->assertInstanceOf('Quantum\Mvc\QtView', $this->viewFactory->getInstance());
    }
    
    public function testProxyCalls()
    {
        $view = $this->viewFactory->getInstance();
        
        $view->setParam('key', 'Value');
        
        $this->assertEquals('Value', $view->getParam('key'));
    }
}
