<?php

namespace Quantum\Http {

    function get_caller_class()
    {
        return 'Quantum\Bootstrap';
    }

    function getallheaders()
    {
        return [];
    }

}

namespace Quantum\Test\Unit {

    use Mockery;
    use Quantum\Libraries\Session\Session;
    use PHPUnit\Framework\TestCase;
    use Quantum\Libraries\Csrf\Csrf;
    use Quantum\Http\HttpRequest;
    use Quantum\Http\Request;

    class RequestTest extends TestCase
    {

        private $server;
        private $session;
        private $cryptor;
        private $sessionData = [];

        public function setUp(): void
        {
            $this->cryptor = Mockery::mock('Quantum\Libraries\Encryption\Cryptor');

            $this->cryptor->shouldReceive('encrypt')->andReturnUsing(function ($arg) {
                return base64_encode($arg);
            });

            $this->cryptor->shouldReceive('decrypt')->andReturnUsing(function ($arg) {
                return base64_decode($arg);
            });

            $this->session = new Session($this->sessionData, $this->cryptor);

            $this->server = Mockery::mock('Quantum\Environment\Server');

            $this->server->shouldReceive('method')->andReturn('GET');

            $this->server->shouldReceive('method')->andReturn('GET');
        }

        public function tearDown(): void
        {
            HttpRequest::flush();
        }

        public function testSetGetMethod()
        {
            $request = new Request();

            $request->create('GET', '/');

            $this->assertEquals('GET', $request->getMethod());

            $request->setMethod('POST');

            $this->assertEquals('POST', $request->getMethod());
        }

        public function testSetGetScheme()
        {
            $request = new Request();

            $request->create('GET', 'https://test.com');

            $this->assertEquals('https', $request->getScheme());

            $request->setScheme('http');

            $this->assertEquals('http', $request->getScheme());
        }

        public function testSetGetHost()
        {
            $request = new Request();

            $request->create('GET', 'https://test.com/dashboard');

            $this->assertEquals('test.com', $request->getHost());

            $request->setHost('tester.com');

            $this->assertEquals('tester.com', $request->getHost());
        }

        public function testSetGetPort()
        {
            $request = new Request();

            $request->create('GET', 'https://test.com:8080/dashboard');

            $this->assertEquals('8080', $request->getPort());

            $request->setPort('9000');

            $this->assertEquals('9000', $request->getPort());
        }

        public function testSetGetUri()
        {
            $request = new Request();

            $request->create('GET', 'http://test.com/post/12');

            $this->assertEquals('post/12', $request->getUri());

            $request->setUri('post/edit/12');

            $this->assertEquals('post/edit/12', $request->getUri());
        }

        public function testSetGetQuery()
        {
            $request = new Request();

            $request->create('GET', 'http://test.com:8080/user?firstname=john&lastname=doe');

            $this->assertEquals('firstname=john&lastname=doe', $request->getQuery());

            $request->create('GET', 'http://test.com:8080/?firstname=john&lastname=doe');

            $this->assertEquals('firstname=john&lastname=doe', $request->getQuery());

            $request->setQuery('age=30&gender=male');

            $this->assertEquals('age=30&gender=male', $request->getQuery());
        }

        public function testRequestSetHasGetDelete()
        {
            $request = new Request();

            $this->assertFalse($request->has('name'));

            $request->set('name', 'John');

            $this->assertTrue($request->has('name'));

            $this->assertEquals('John', $request->get('name'));

            $request->delete('name');

            $this->assertNotEquals('John', $request->get('name'));
        }

        public function testRequestAll()
        {
            $request = new Request();

            $this->assertEmpty($request->all());

            $request->set('name', 'John');

            $this->assertNotEmpty($request->all());

            $this->assertIsArray($request->all());
        }

        public function testHasGetFile()
        {
            $request = new Request();
            
            $this->assertFalse($request->hasFile('image'));

            $file = [
                'image' => [
                    'size' => 500,
                    'name' => 'foo.jpg',
                    'tmp_name' => __FILE__,
                    'type' => 'image/jpg',
                    'error' => 0,
                ],
            ];
            
            $request->create('POST', '/upload', $file);
            
            $this->assertTrue($request->hasFile('image'));
            
            $this->assertIsArray($request->getFile('image'));
            
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

        public function testGetSegments()
        {
            $request = new Request();

            $request->create('GET', 'post/12/notes');

            $this->assertIsArray($request->getAllSegments());

            $this->assertEquals('post', $request->getSegment(1));

            $this->assertEquals('12', $request->getSegment(2));

            $this->assertNull($request->getSegment(10));
        }

        public function testGetCSRFToken()
        {
            $request = new Request();

            $this->assertNull($request->getCSRFToken());

            $token = Csrf::generateToken($this->session, 'token');

            $request->set('token', $token);

            $this->assertNotNull($request->getCSRFToken());
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

        public function testIsAjax()
        {
            $request = new Request();

            $request->create('POST', '/save');

            $request->setHeader('X-REQUESTED-WITH', 'xmlhttprequest');

            $this->assertTrue($request->isAjax());
        }

        public function testGetCurrentUrl()
        {
            $request = new Request();

            $request->create('GET', 'https://test.com/');

            $this->assertEquals('https://test.com/', $request->getCurrentUrl());

            $request->create('GET', 'http://test.com/user/12');

            $this->assertEquals('http://test.com/user/12', $request->getCurrentUrl());

            $request->create('GET', 'http://test.com/user/12?firstname=John&lastname=Doe');

            $this->assertEquals('http://test.com/user/12?firstname=John&lastname=Doe', $request->getCurrentUrl());

            $request->create('GET', 'http://test.com/?firstname=John&lastname=Doe');

            $this->assertEquals('http://test.com/?firstname=John&lastname=Doe', $request->getCurrentUrl());

            $request->create('GET', 'http://test.com:8080/?firstname=John&lastname=Doe');

            $this->assertEquals('http://test.com:8080/?firstname=John&lastname=Doe', $request->getCurrentUrl());
        }

    }

}