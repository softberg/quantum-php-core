<?php

namespace Quantum\Http\Response {

    use Quantum\Bootstrap;

    function get_caller_class()
    {
        return Bootstrap::class;
    }

}

namespace Quantum\Tests\Http {

    use PHPUnit\Framework\TestCase;
    use Quantum\Exceptions\StopExecutionException;
    use Quantum\Http\Response;
    use Quantum\Di\Di;
    use Quantum\App;


    class ResponseTest extends TestCase
    {

        public function setUp(): void
        {
            App::loadCoreFunctions(dirname(__DIR__, 3) . DS . 'src' . DS . 'Helpers');

            App::setBaseDir(dirname(__DIR__) . DS . '_root');

            Di::loadDefinitions();

            Response::init();
        }

        public function tearDown(): void
        {
            Response::flush();
        }

        public function testResponseSetHasGetAllDelete()
        {
            $response = new Response();

            $this->assertEmpty($response->all());

            $this->assertFalse($response->has('name'));

            $response->set('name', 'John');

            $this->assertTrue($response->has('name'));

            $this->assertEquals('John', $response->get('name'));

            $this->assertIsArray($response->all());

            $response->delete('name');

            $this->assertFalse($response->has('name'));

            $this->assertNull($response->get('name'));

            $this->assertEquals('Jane', $response->get('name', 'Jane'));
        }

        public function testResponseHeaderSetHasGetAllDelete()
        {
            $response = new Response();

            $this->assertEmpty($response->allHeaders());

            $this->assertFalse($response->hasHeader('X-Frame-Options'));

            $response->setHeader('X-Frame-Options', 'deny');

            $this->assertTrue($response->hasHeader('X-Frame-Options'));

            $this->assertEquals('deny', $response->getHeader('X-Frame-Options'));

            $this->assertIsArray($response->allHeaders());

            $response->deleteHeader('X-Frame-Options');

            $this->assertFalse($response->hasHeader('X-Frame-Options'));

            $this->assertNull($response->getHeader('X-Frame-Options'));
        }

        public function testResponseStatus()
        {
            $response = new Response();

            $this->assertEquals(200, $response->getStatusCode());

            $response->setStatusCode(301);

            $this->assertEquals(301, $response->getStatusCode());

            $this->assertEquals('Moved Permanently', $response->getStatusText());
        }

        public function testResponseContentType()
        {
            $response = new Response();

            $this->assertNull($response->getContentType());

            $response->setContentType('application/json');

            $this->assertEquals('application/json', $response->getContentType());
        }

        public function testResponseRedirect()
        {
            $response = new Response();

            $this->assertFalse($response->hasHeader('Location'));

            try {
                $response->redirect('/');
            } catch (StopExecutionException $e) {
            }

            $this->assertTrue($response->hasHeader('Location'));

            $this->assertEquals('/', $response->getHeader('Location'));

            $this->assertEquals(200, $response->getStatusCode());

            try {
                $response->redirect('/home', 301);
            } catch (StopExecutionException $e) {
            }

            $this->assertEquals('/home', $response->getHeader('Location'));

            $this->assertEquals(301, $response->getStatusCode());
        }

        public function testResponseJsonContent()
        {
            $response = new Response();

            $response->set('firstname', 'John');

            $response->set('lastname', 'Doe');

            $response->json();

            $this->assertEquals('{"firstname":"John","lastname":"Doe"}', $response->getContent());

            $response->set('age', 25);

            $this->assertEquals('{"firstname":"John","lastname":"Doe","age":25}', $response->getContent());

            $response->json([
                'gender' => 'male',
                'role' => 'user'
            ], 200);

            $this->assertEquals('{"firstname":"John","lastname":"Doe","age":25,"gender":"male","role":"user"}', $response->getContent());

            $this->assertEquals(200, $response->getStatusCode());
        }

        public function testReponseXmlContent()
        {
            $response = new Response();

            $response->set('firstname', 'John');

            $response->set('lastname', 'Doe');

            $response->xml();

            $xml = "<?xml version=\"1.0\"?>\n" .
                "<data>\n" .
                "  <firstname>John</firstname>\n" .
                "  <lastname>Doe</lastname>\n" .
                "</data>\n";

            $this->assertEquals($xml, $response->getContent());

            $response->set('age', 25);

            $xml = "<?xml version=\"1.0\"?>\n" .
                "<data>\n" .
                "  <firstname>John</firstname>\n" .
                "  <lastname>Doe</lastname>\n" .
                "  <age>25</age>\n" .
                "</data>\n";

            $this->assertEquals($xml, $response->getContent());

            $response->xml([
                'gender' => 'male',
                'role' => 'user'
            ]);

            $xml = "<?xml version=\"1.0\"?>\n" .
                "<data>\n" .
                "  <firstname>John</firstname>\n" .
                "  <lastname>Doe</lastname>\n" .
                "  <age>25</age>\n" .
                "  <gender>male</gender>\n" .
                "  <role>user</role>\n" .
                "</data>\n";

            $this->assertEquals($xml, $response->getContent());
        }

        public function testResponseXmlWithNestedArray()
        {
            $response = new Response();

            $response->xml([
                'article' => [
                    'title' => 'Todays news',
                    'description' => 'News content'
                ]
            ]);

            $xml = "<?xml version=\"1.0\"?>\n" .
                "<data>\n" .
                "  <article>\n" .
                "    <title>Todays news</title>\n" .
                "    <description>News content</description>\n" .
                "  </article>\n" .
                "</data>\n";

            $this->assertEquals($xml, $response->getContent());
        }

        public function testResponseXmlWithArguments()
        {
            $response = new Response();

            $response->xml([
                'article@{"type":"post"}' => [
                    'title' => 'Todays news',
                    'description@{"content":"html"}' => 'News content'
                ]
            ]);

            $xml = "<?xml version=\"1.0\"?>\n" .
                "<data>\n" .
                "  <article type=\"post\">\n" .
                "    <title>Todays news</title>\n" .
                "    <description content=\"html\">News content</description>\n" .
                "  </article>\n" .
                "</data>\n";

            $this->assertEquals($xml, $response->getContent());
        }

        public function testResponseXmlWithCustomRoot()
        {
            $response = new Response();

            $response->xml([
                'article@{"type":"post"}' => [
                    'title' => 'Todays news',
                    'description@{"content":"html"}' => 'News content'
                ]
            ], '<custom></custom>', 200);

            $xml = "<?xml version=\"1.0\"?>\n" .
                "<custom>\n" .
                "  <article type=\"post\">\n" .
                "    <title>Todays news</title>\n" .
                "    <description content=\"html\">News content</description>\n" .
                "  </article>\n" .
                "</custom>\n";

            $this->assertEquals($xml, $response->getContent());
        }

        public function testResponseHtmlContent()
        {
            $response = new Response();

            $response->html('<div>John Doe</div>');

            $this->assertEquals('<div>John Doe</div>', $response->getContent());

            $this->assertEquals(200, $response->getStatusCode());
        }

    }

}