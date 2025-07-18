<?php

namespace Http\Traits\Response;

use Quantum\Tests\Unit\AppTestCase;
use Quantum\Http\Response;

class HttpResponseBodyTest extends AppTestCase
{

    public function setUp(): void
    {
        parent::setUp();
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
        ]);

        $this->assertEquals('{"firstname":"John","lastname":"Doe","age":25,"gender":"male","role":"user"}', $response->getContent());

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testResponseJsonP()
    {
        $response = new Response();

        $response->set('firstname', 'John');

        $response->set('lastname', 'Doe');

        $response->jsonp('myfunc');

        $this->assertEquals('myfunc({"firstname":"John","lastname":"Doe"})', $response->getContent());
    }

    public function testResponseXmlContent()
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