<?php

namespace Http\Traits\Request;

use Quantum\Tests\Unit\AppTestCase;
use Quantum\Environment\Server;
use Quantum\Http\Request;

class HttpRequestHeaderTest extends AppTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Request::init(Server::getInstance());
    }

    public function tearDown(): void
    {
        Request::flush();

        Server::getInstance()->flush();
    }

    public function testRequestHeaderSetHasGetDelete()
    {
        $request = new Request();

        $this->assertFalse($request->hasHeader('name'));

        $request->setHeader('X-CUSTOM', 'Custom');

        $this->assertTrue($request->hasHeader('X-CUSTOM'));

        $this->assertEquals('Custom', $request->getHeader('X-CUSTOM'));

        $request->delete('X-CUSTOM');

        $this->assertNotEquals('Custom', $request->get('X-CUSTOM'));
    }

    public function testRequestHeaderAll()
    {
        $request = new Request();

        $this->assertEmpty($request->allHeaders());

        $request->setHeader('X-CUSTOM', 'Custom');

        $this->assertNotEmpty($request->allHeaders());

        $this->assertIsArray($request->allHeaders());
    }

    public function testGetAuthorizationBearer()
    {
        $request = new Request();

        $bearerToken = md5('random');

        $this->assertNull($request->getAuthorizationBearer());

        $request->setHeader('Authorization', 'Bearer ' . $bearerToken);

        $this->assertNotNull($request->getAuthorizationBearer());

        $this->assertEquals($bearerToken, $request->getAuthorizationBearer());
    }

    public function testGetBasicAuthCredentialsFromServer()
    {
        $request = new Request();

        $server = Server::getInstance();

        $credentials = [
            'username' => 'testGlobalUsername',
            'password' => 'testGlobalPassword',
        ];

        $this->assertNull($request->getBasicAuthCredentials());

        $server->set('PHP_AUTH_USER', $credentials['username']);

        $server->set('PHP_AUTH_PW', $credentials['password']);

        $result = $request->getBasicAuthCredentials();

        $this->assertNotNull($result);

        $this->assertEquals($credentials, $result);

        unset($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']);
    }

    public function testGetBasicAuthCredentialsFromHeader()
    {
        $request = new Request();

        $username = 'testHeaderName';

        $password = 'testHeaderPassword';

        $credentials = base64_encode("$username:$password");

        $this->assertNull($request->getBasicAuthCredentials());

        $request->setHeader('Authorization', 'Basic ' . $credentials);

        $result = $request->getBasicAuthCredentials();

        $this->assertNotNull($result);

        $this->assertEquals($username, $result['username']);

        $this->assertEquals($password, $result['password']);
    }

    public function testIsAjaxReturnsTrueWhenHeaderIsSet()
    {
        $request = new Request();

        $this->assertFalse($request->isAjax());

        $request->setHeader('X-Requested-With', 'XMLHttpRequest');

        $this->assertTrue($request->isAjax());
    }

    public function testIsAjaxReturnsTrueFromServerWhenHeaderIsMissing()
    {
        $request = new Request();

        $this->assertFalse($request->isAjax());

        Server::getInstance()->set('HTTP_X_REQUESTED_WITH', 'XMLHttpRequest');

        $this->assertTrue($request->isAjax());
    }

    public function testGetReferrer()
    {
        $this->assertNull(Request::getReferrer());

        $referrer = 'https://example.com/page';

        Server::getInstance()->set('HTTP_REFERER', $referrer);

        $this->assertEquals($referrer, Request::getReferrer());
    }
}
