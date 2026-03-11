<?php

namespace Quantum\Tests\Unit\View\Factories;

use Quantum\View\Factories\ViewFactory;
use Quantum\Tests\Unit\AppTestCase;
use Quantum\View\QtView;

class ViewFactoryTest extends AppTestCase
{
    private ViewFactory $viewFactory;

    public function setUp(): void
    {
        parent::setUp();

        $this->viewFactory = new ViewFactory();
    }

    public function testGetInstance(): void
    {
        $this->assertInstanceOf(QtView::class, $this->viewFactory->get());
    }

    public function testProxyCalls(): void
    {
        $view = $this->viewFactory->get();

        $view->setParam('key', 'Value');

        $this->assertEquals('Value', $view->getParam('key'));
    }

}
