<?php

namespace Quantum\Tests\Unit\Environment;

use Quantum\Tests\Unit\AppTestCase;
use Quantum\Environment\Server;

class ServerTest extends AppTestCase
{

    public function setUp(): void
    {
        parent::setUp();

        $this->setPrivateProperty(Server::class, 'instance', null);
    }

    public function testServerGetInstance()
    {
        $server1 = Server::getInstance();
        $server2 = Server::getInstance();

        $this->assertSame($server1, $server2);
    }

    public function testServerAll()
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

    public function testServerSetAndGet()
    {
        $server = Server::getInstance();

        $server->set('REQUEST_METHOD', 'POST');

        $this->assertEquals('POST', $server->get('REQUEST_METHOD'));

        $server->set('CUSTOM_KEY', 'custom_value');

        $this->assertEquals('custom_value', $server->get('CUSTOM_KEY'));

        $this->assertNull($server->get('NON_EXISTENT_KEY'));
    }

    public function testServerUri()
    {
        $server = Server::getInstance();
        $server->set('REQUEST_URI', '/test/uri');

        $this->assertEquals('/test/uri', $server->uri());
    }

    public function testServerQuery()
    {
        $server = Server::getInstance();
        $server->set('QUERY_STRING', 'foo=bar');

        $this->assertEquals('foo=bar', $server->query());
    }

    public function testServerMethod()
    {
        $server = Server::getInstance();
        $server->set('REQUEST_METHOD', 'PUT');

        $this->assertEquals('PUT', $server->method());
    }

    public function testServerProtocol()
    {
        $server = Server::getInstance();
        $server->set('HTTPS', 'on');
        $server->set('SERVER_PORT', 443);

        $this->assertEquals('https', $server->protocol());

        $server->set('HTTPS', null);
        $server->set('SERVER_PORT', 80);

        $this->assertEquals('http', $server->protocol());
    }

    public function testServerHost()
    {
        $server = Server::getInstance();
        $server->set('SERVER_NAME', 'localhost');

        $this->assertEquals('localhost', $server->host());
    }

    public function testServerPort()
    {
        $server = Server::getInstance();
        $server->set('SERVER_PORT', '9000');

        $this->assertEquals('9000', $server->port());
    }

    public function testServerContentType()
    {
        $server = Server::getInstance();
        $server->set('CONTENT_TYPE', 'application/json; charset=utf-8');

        $this->assertEquals('application/json; charset=utf-8', $server->contentType());
        $this->assertEquals('application/json', $server->contentType(true));
    }

    public function testServerReferrer()
    {
        $server = Server::getInstance();
        $server->set('HTTP_REFERER', 'http://example.com');

        $this->assertEquals('http://example.com', $server->referrer());
    }

    public function testServerAjax()
    {
        $server = Server::getInstance();
        $server->set('HTTP_X_REQUESTED_WITH', 'XMLHttpRequest');

        $this->assertTrue($server->ajax());

        $server->set('HTTP_X_REQUESTED_WITH', null);

        $this->assertFalse($server->ajax());
    }

    public function testServerGetUserIpFromRemoteAddr()
    {
        $server = Server::getInstance();

        $server->set('HTTP_CLIENT_IP', '192.168.1.1');
        $server->set('HTTP_X_FORWARDED_FOR', null);
        $server->set('REMOTE_ADDR', null);
        $this->assertEquals('192.168.1.1', $server->ip());

        $server->set('HTTP_CLIENT_IP', null);
        $server->set('HTTP_X_FORWARDED_FOR', '203.0.113.5');
        $server->set('REMOTE_ADDR', null);
        $this->assertEquals('203.0.113.5', $server->ip());

        $server->set('HTTP_CLIENT_IP', null);
        $server->set('HTTP_X_FORWARDED_FOR', null);
        $server->set('REMOTE_ADDR', '198.51.100.1');
        $this->assertEquals('198.51.100.1', $server->ip());

        $server->set('HTTP_CLIENT_IP', null);
        $server->set('HTTP_X_FORWARDED_FOR', null);
        $server->set('REMOTE_ADDR', null);
        $this->assertNull($server->ip());
    }

    public function testServerGetAllHeadersFromServerClass()
    {
        $server = Server::getInstance();

        $server->set('HTTP_USER_AGENT', 'Mozilla/5.0');
        $server->set('HTTP_ACCEPT', 'text/html');
        $server->set('HTTP_X_CUSTOM_HEADER', 'CustomValue');
        $server->set('SERVER_NAME', 'example.com');

        $headers = $server->getAllHeaders();

        $this->assertArrayHasKey('user-agent', $headers);
        $this->assertArrayHasKey('accept', $headers);
        $this->assertArrayHasKey('x-custom-header', $headers);

        $this->assertEquals('Mozilla/5.0', $headers['user-agent']);
        $this->assertEquals('text/html', $headers['accept']);
        $this->assertEquals('CustomValue', $headers['x-custom-header']);

        $this->assertArrayNotHasKey('SERVER_NAME', $headers);
    }

    public function testServerAcceptedLang()
    {
        $server = Server::getInstance();

        $server->set('HTTP_ACCEPT_LANGUAGE', null);
        $this->assertNull($server->acceptedLang());

        $server->set('HTTP_ACCEPT_LANGUAGE', 'en-US');
        $this->assertEquals('en', $server->acceptedLang());

        $server->set('HTTP_ACCEPT_LANGUAGE', 'fr-CA,fr;q=0.8,en-US;q=0.6,en;q=0.4');
        $this->assertEquals('fr', $server->acceptedLang());

        $server->set('HTTP_ACCEPT_LANGUAGE', '  de-DE , en-US ');
        $this->assertEquals('de', $server->acceptedLang());

        $server->set('HTTP_ACCEPT_LANGUAGE', 'ES-ES');
        $this->assertEquals('es', $server->acceptedLang());
    }

}