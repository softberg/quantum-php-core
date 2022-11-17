<?php

namespace Quantum\Renderer {

    function current_module()
    {
        return 'test';
    }

}

namespace Quantum\Tests\Mvc {

    use Quantum\Exceptions\ViewException;
    use Quantum\Factory\ViewFactory;
    use Quantum\Tests\AppTestCase;


    class QtViewTest extends AppTestCase
    {

        private $view;

        public function setUp(): void
        {
            parent::setUp();
            
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

            $this->expectExceptionMessage('layout_not_set');

            $this->view->render('index');
        }

        public function testRenderWithLayout()
        {
            $this->view->setLayout('layout');

            $renderedView = $this->view->render('index');

            $this->assertIsString($renderedView);

            $this->assertEquals('<html>' . PHP_EOL . '<head></head>' . PHP_EOL . '<body>' . PHP_EOL . '<p>Hello World, this is rendered view</p></body>' . PHP_EOL . '</html>' . PHP_EOL, $renderedView);
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
