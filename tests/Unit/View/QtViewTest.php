<?php

namespace Quantum\Tests\Unit\View;

use Quantum\View\Exceptions\ViewException;
use Quantum\View\Factories\ViewFactory;
use Quantum\Tests\Unit\AppTestCase;
use Quantum\Router\Router;
use Quantum\View\RawParam;


class QtViewTest extends AppTestCase
{

    private $view;

    public function setUp(): void
    {
        parent::setUp();

        Router::setCurrentRoute([
            "route" => "test",
            "method" => "GET",
            "controller" => "SomeController",
            "action" => "test",
            "module" => "Test"
        ]);

        $this->view = ViewFactory::get();
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

    public function testSetGetRawParams()
    {
        $this->assertEmpty($this->view->getParams());

        $this->view->setParam('title', 'Jungle book');

        $this->view->setParam('summery', new RawParam('<summery>do not judge a book by its cover<summery>'));

        $this->view->setRawParam('header', '<header>Mowgli in jungle</header>');

        $this->view->setParams([
            'chapter_one' => new RawParam('<p>Mowglis brothers</p>'),
            'chapter_two' => new RawParam('<p>Monkey people</p>'),
        ]);

        $this->assertIsArray($this->view->getParams());

        $this->assertEquals('Jungle book', $this->view->getParam('title'));

        $this->assertEquals('<summery>do not judge a book by its cover<summery>', $this->view->getParam('summery'));

        $this->assertEquals('<header>Mowgli in jungle</header>', $this->view->getParam('header'));

        $this->assertEquals('<p>Mowglis brothers</p>', $this->view->getParam('chapter_one'));

        $this->assertEquals('<p>Monkey people</p>', $this->view->getParam('chapter_two'));
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

        $this->assertEquals('<html>' . PHP_EOL . '<head></head>' . PHP_EOL . '<body>' . PHP_EOL . '<p>Hello World, this is rendered html view</p></body>' . PHP_EOL . '</html>' . PHP_EOL, $renderedView);
    }

    public function testRenderWithData()
    {
        $this->view->setLayout('layout');

        $this->view->render('index', ['name' => 'Lorem Ipsum']);

        $this->assertEquals('<p>Hello Lorem Ipsum, this is rendered html view</p>', $this->view->getView());

        $this->view->setParam('name', 'dolor sit amet');

        $this->view->render('index');

        $this->assertEquals('<p>Hello dolor sit amet, this is rendered html view</p>', $this->view->getView());
    }

    public function testRenderWithEscapedHtmlData()
    {
        $this->view->setLayout('layout');

        $this->view->setParam('content', '<h1>Hello</h1>');

        $this->view->render('post');

        $this->assertEquals('&lt;h1&gt;Hello&lt;/h1&gt;', $this->view->getView());
    }

    public function testRenderWithUnEscapedHtmlData()
    {
        $this->view->setLayout('layout');

        $this->view->setParam('content', new RawParam('<h1>Hello</h1>'));

        $this->view->render('post');

        $this->assertEquals('<h1>Hello</h1>', $this->view->getView());
    }

    public function testRenderPartial()
    {
        $this->assertIsString($this->view->renderPartial('partial'));

        $this->assertEquals('<p>Hello World, this is rendered partial html view</p>', $this->view->renderPartial('partial'));

        $this->assertEquals('<p>Hello Tester, this is rendered partial html view</p>', $this->view->renderPartial('partial', ['name' => 'Tester']));
    }

    public function testRenderViewWithTwig(): void
    {
        $this->setPrivateProperty(ViewFactory::class, 'instance', null);

        config()->set('view.default', 'twig');
        config()->set('view.twig', ['autoescape' => false]);

        $view = ViewFactory::get();

        $view->setLayout('layout.twig');

        $renderedView = $view->render('index.twig', ['name' => 'Tester']);

        $this->assertIsString($renderedView);

        $renderedView = str_replace("\n", PHP_EOL, $renderedView);

        $this->assertEquals('<html>' . PHP_EOL . '<head></head>' . PHP_EOL . '<body>' . PHP_EOL . '<p>Hello Tester, this is rendered twig view</p>' . PHP_EOL . '</body>' . PHP_EOL . '</html>' . PHP_EOL, $renderedView);
    }
}