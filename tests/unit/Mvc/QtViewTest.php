<?php

namespace {

    use Quantum\Factory\ViewFactory;

    function view()
    {
        return ViewFactory::getInstance()->getView();
    }

}

namespace Quantum\Mvc {

    function get_caller_class()
    {
        return 'Quantum\Factory\ViewFactory';
    }

    function modules_dir()
    {
        return __DIR__ . DS . 'modules';
    }

    function current_module()
    {
        return 'test';
    }

}

namespace Quantum\Test\Unit {

    use PHPUnit\Framework\TestCase;
    use Quantum\Exceptions\ViewException;
    use Quantum\Libraries\Storage\FileSystem;
    use Quantum\Loader\Loader;
    use Quantum\Mvc\QtView;

    class QtViewTest extends TestCase
    {

        private $view;
        private $viewsDir;
        private $layoutContent = '<html><head></head><body></body></html>';
        private $viewOneContent = '<h1>Hello</h1>';
        private $viewTwoContent = '<h1><?php echo $text ?></h1>';

        public function setUp(): void
        {
            $loader = new Loader(new FileSystem);
            
            $loader->loadDir(dirname(__DIR__, 3) . DS . 'src' . DS . 'Helpers' . DS . 'functions');

            $this->view = new QtView();
            
            $this->viewsDir = \Quantum\Mvc\modules_dir() . DS . \Quantum\Mvc\current_module() . DS . 'Views';

            if (!is_dir($this->viewsDir))
                mkdir($this->viewsDir, 0777, true);

            file_put_contents($this->viewsDir . DS . 'layout.php', $this->layoutContent);

            file_put_contents($this->viewsDir . DS . 'index.php', $this->viewOneContent);

            file_put_contents($this->viewsDir . DS . 'content.php', $this->viewTwoContent);
        }

        public function tearDown(): void
        {
            unlink($this->viewsDir . DS . 'layout.php');
            unlink($this->viewsDir . DS . 'index.php');
            unlink($this->viewsDir . DS . 'content.php');
            
            sleep(1);
            rmdir($this->viewsDir);
            
            sleep(1);
            rmdir(\Quantum\Mvc\modules_dir() . DS . \Quantum\Mvc\current_module());
            
            sleep(1);
            rmdir(\Quantum\Mvc\modules_dir());
        }

        public function testSetGetLayout()
        {
            $this->assertNull($this->view->getLayout());

            $this->view->setLayout('/someLayout');

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

        public function testRenderWithouthLayout()
        {
            $this->expectException(ViewException::class);

            $this->expectExceptionMessage('Layout is not set');

            $this->view->render('index');
        }

        public function testRenderWithLayout()
        {
            $this->view->setLayout('layout');

            $this->assertIsString($this->view->render('index'));

            $this->assertEquals($this->layoutContent, $this->view->render('index'));
        }

        public function testRenderWithData()
        {
            $this->view->setLayout('layout');

            $this->view->render('content', ['text' => 'Lorem Ipsum']);

            $this->assertEquals('<h1>Lorem Ipsum</h1>', $this->view->getView());

            $this->view->setParam('text', 'dolor sit amet');

            $this->view->render('content');

            $this->assertEquals('<h1>dolor sit amet</h1>', $this->view->getView());
        }

        public function testRenderPartial()
        {
            $this->assertIsString($this->view->renderPartial('index'));

            $this->assertEquals('<h1>Hello from partial</h1>', $this->view->renderPartial('content', ['text' => 'Hello from partial']));

            $this->assertEquals('<h1>Hello &lt;div&gt;from&lt;/div&gt; partial</h1>', $this->view->renderPartial('content', ['text' => 'Hello <div>from</div> partial']));
        }

    }

}
