<?php

namespace Quantum\Test\Unit;

use PHPUnit\Framework\TestCase;
use Quantum\Http\Response;

class ResponseTest extends TestCase
{

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

        $this->assertNull($response->getStatusCode());

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

        $response->redirect('/');

        $this->assertTrue($response->hasHeader('Location'));

        $this->assertEquals('/', $response->getHeader('Location'));

        $this->assertNull($response->getStatusCode());

        $response->redirect('/home', 301);

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
                "<data>" .
                "<firstname>John</firstname>" .
                "<lastname>Doe</lastname>" .
                "</data>\n";

        $this->assertEquals($xml, $response->getContent());

        $response->set('age', 25);

        $xml = "<?xml version=\"1.0\"?>\n" .
                "<data>" .
                "<firstname>John</firstname>" .
                "<lastname>Doe</lastname>" .
                "<age>25</age>" .
                "</data>\n";

        $this->assertEquals($xml, $response->getContent());

        $response->xml([
            'gender' => 'male',
            'role' => 'user'
        ], 200);

        $xml = "<?xml version=\"1.0\"?>\n" .
                "<data>" .
                "<firstname>John</firstname>" .
                "<lastname>Doe</lastname>" .
                "<age>25</age>" .
                "<gender>male</gender>" .
                "<role>user</role>" .
                "</data>\n";

        $this->assertEquals($xml, $response->getContent());
    }

}
