<?php

namespace Quantum\Tests\Environment;

use Quantum\Environment\Server;
use Quantum\Tests\AppTestCase;
use ReflectionClass;

class ServerTest extends AppTestCase
{

    public function setUp(): void
    {
        parent::setUp();

        $reflection = new ReflectionClass(Server::class);
        $instance = $reflection->getProperty('instance');
        $instance->setAccessible(true);
        $instance->setValue(null);
    }

    public function testGetInstance()
    {
        $server1 = Server::getInstance();
        $server2 = Server::getInstance();

        $this->assertSame($server1, $server2);
    }

    public function testAll()
    {
        $server = Server::getInstance();
        $server->set('REQUEST_METHOD', 'GET');
        $server->set('REQUEST_URI', '/test');

        $allServerData = $server->all();

        $this->assertArrayHasKey('REQUEST_METHOD', $allServerData);
        $this->assertArrayHasKey('REQUEST_URI', $allServerData);

        $this->assertEquals('GET', $allServerData['REQUEST_METHOD']);
        $this->assertEquals('/test', $allServerData['REQUEST_URI']);
    }

    public function testGet()
    {
        $server = Server::getInstance();
        $server->set('REQUEST_METHOD', 'POST');

        $this->assertEquals('POST', $server->get('REQUEST_METHOD'));
        $this->assertNull($server->get('NON_EXISTENT_KEY'));
    }

    public function testSet()
    {
        $server = Server::getInstance();
        $server->set('CUSTOM_KEY', 'custom_value');

        $this->assertEquals('custom_value', $server->get('CUSTOM_KEY'));
    }

    public function testUri()
    {
        $server = Server::getInstance();
        $server->set('REQUEST_URI', '/test/uri');

        $this->assertEquals('/test/uri', $server->uri());
    }

    public function testQuery()
    {
        $server = Server::getInstance();
        $server->set('QUERY_STRING', 'foo=bar');

        $this->assertEquals('foo=bar', $server->query());
    }

    public function testMethod()
    {
        $server = Server::getInstance();
        $server->set('REQUEST_METHOD', 'PUT');

        $this->assertEquals('PUT', $server->method());
    }

    public function testProtocol()
    {
        $server = Server::getInstance();
        $server->set('HTTPS', 'on');
        $server->set('SERVER_PORT', 443);

        $this->assertEquals('https', $server->protocol());

        $server->set('HTTPS', null);
        $server->set('SERVER_PORT', 80);

        $this->assertEquals('http', $server->protocol());
    }

    public function testHost()
    {
        $server = Server::getInstance();
        $server->set('SERVER_NAME', 'localhost');

        $this->assertEquals('localhost', $server->host());
    }

    public function testPort()
    {
        $server = Server::getInstance();
        $server->set('SERVER_PORT', '9000');

        $this->assertEquals('9000', $server->port());
    }

    public function testContentType()
    {
        $server = Server::getInstance();
        $server->set('CONTENT_TYPE', 'application/json; charset=utf-8');

        $this->assertEquals('application/json; charset=utf-8', $server->contentType());
        $this->assertEquals('application/json', $server->contentType(true));
    }

    public function testReferrer()
    {
        $server = Server::getInstance();
        $server->set('HTTP_REFERER', 'http://example.com');

        $this->assertEquals('http://example.com', $server->referrer());
    }

    public function testAjax()
    {
        $server = Server::getInstance();
        $server->set('HTTP_X_REQUESTED_WITH', 'XMLHttpRequest');

        $this->assertTrue($server->ajax());

        $server->set('HTTP_X_REQUESTED_WITH', null);

        $this->assertFalse($server->ajax());
    }

}