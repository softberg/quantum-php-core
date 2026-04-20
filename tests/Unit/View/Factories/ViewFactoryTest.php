<?php

namespace Quantum\Tests\Unit\View\Factories;

use Quantum\View\Factories\ViewFactory;
use Quantum\Tests\Unit\AppTestCase;
use Quantum\View\QtView;
use Quantum\Di\Di;

class ViewFactoryTest extends AppTestCase
{
    private ViewFactory $viewFactory;

    public function setUp(): void
    {
        parent::setUp();

        $this->resetViewFactory();

        if (!Di::isRegistered(ViewFactory::class)) {
            Di::register(ViewFactory::class);
        }

        $this->viewFactory = Di::get(ViewFactory::class);
    }

    public function testGetInstance(): void
    {
        $this->assertInstanceOf(QtView::class, ViewFactory::get());
    }

    public function testResolveReturnsSameInstance(): void
    {
        $view1 = $this->viewFactory->resolve();
        $view2 = $this->viewFactory->resolve();

        $this->assertSame($view1, $view2);
    }

    public function testProxyCalls(): void
    {
        $view = ViewFactory::get();

        $view->setParam('key', 'Value');

        $this->assertEquals('Value', $view->getParam('key'));
    }

    private function resetViewFactory(): void
    {
        if (!Di::isRegistered(ViewFactory::class)) {
            Di::register(ViewFactory::class);
        }

        $factory = Di::get(ViewFactory::class);
        $this->setPrivateProperty($factory, 'instance', null);
    }
}
