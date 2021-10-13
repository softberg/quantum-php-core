<?php

namespace Quantum\Renderer {

    function current_module()
    {
        return 'test';
    }

}

namespace Quantum\Test\Unit {

    use PHPUnit\Framework\TestCase;
    use Quantum\Exceptions\ViewException;
    use Quantum\Factory\ViewFactory;
    use Quantum\Di\Di;
    use Quantum\App;

    /**
     * @runTestsInSeparateProcesses
     */
    class QtViewTest extends TestCase
    {

        private $view;

        public function setUp(): void
        {
            App::loadCoreFunctions(dirname(__DIR__, 3) . DS . 'src' . DS . 'Helpers');

            App::setBaseDir(dirname(__DIR__) . DS . '_root');

            Di::loadDefinitions();

            $this->view = ViewFactory::getInstance();
        }

        public function tearDown(): void
        {
            $this->view->setLayout(null);

            $this->view->flushParams();
        }

        public function testSetGetLayout()
        {
            $this->assertNull($this->view->getLayout());

            $this->view->setLayout('layout');

            $this->assertNotNull($this->view->getLayout());

            $this->assertIsString($this->view->getLayout());
        }

        public function testSetGetParams()
        {
            $this->assertEmpty($this->view->getParams());

            $this->assertNull($this->view->getParam('firstname'));

            $this->view->setParam('firstname', 'John');

            $this->assertEquals('John', $this->view->getParam('firstname'));

            $this->view->setParams(['lastname' => 'Doe', 'age' => 35]);

            $this->assertEquals('Doe', $this->view->getParam('lastname'));

            $this->assertEquals(35, $this->view->getParam('age'));

            $this->assertNotEmpty($this->view->getParams());
        }

        public function testRenderWithoutLayout()
        {
            $this->expectException(ViewException::class);

            $this->expectExceptionMessage('Layout is not set');

            $this->view->render('index');
        }

        public function testRenderWithLayout()
        {
            $this->view->setLayout('layout');

            $renderedView = $this->view->render('index');

            $this->assertIsString($renderedView);

            $this->assertEquals("<html>\r\n<head></head>\r\n<body>\r\n<p>Hello World, this is rendered view</p></body>\r\n</html>\r\n", $renderedView);
        }

        public function testRenderWithData()
        {
            $this->view->setLayout('layout');

            $this->view->render('index', ['name' => 'Lorem Ipsum']);

            $this->assertEquals('<p>Hello Lorem Ipsum, this is rendered view</p>', $this->view->getView());

            $this->view->setParam('name', 'dolor sit amet');

            $this->view->render('index');

            $this->assertEquals('<p>Hello dolor sit amet, this is rendered view</p>', $this->view->getView());
        }

        public function testRenderPartial()
        {
            $this->assertIsString($this->view->renderPartial('partial'));

            $this->assertEquals('<p>Hello World, this is rendered partial view</p>', $this->view->renderPartial('partial'));

            $this->assertEquals('<p>Hello Tester, this is rendered partial view</p>', $this->view->renderPartial('partial', ['name' => 'Tester']));
        }

    }

}
