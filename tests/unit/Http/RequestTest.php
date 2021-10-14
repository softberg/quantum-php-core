<?php

namespace Quantum\Test\Unit;

use Mockery;
use PHPUnit\Framework\TestCase;
use Quantum\Exceptions\FileUploadException;
use Quantum\Libraries\Session\Session;
use Quantum\Http\Request\HttpRequest;
use Quantum\Libraries\Upload\File;
use Quantum\Libraries\Csrf\Csrf;
use Quantum\Http\Request;
use Quantum\Di\Di;
use Quantum\App;

class RequestTest extends TestCase
{

    private $session;
    private $sessionData = [];

    public function setUp(): void
    {
        App::loadCoreFunctions(dirname(__DIR__, 3) . DS . 'src' . DS . 'Helpers');

        App::setBaseDir(dirname(__DIR__) . DS . '_root');

        Di::loadDefinitions();

        $cryptor = Mockery::mock('Quantum\Libraries\Encryption\Cryptor');

        $cryptor->shouldReceive('encrypt')->andReturnUsing(function ($arg) {
            return base64_encode($arg);
        });

        $cryptor->shouldReceive('decrypt')->andReturnUsing(function ($arg) {
            return base64_decode($arg);
        });

        $this->session = new Session($this->sessionData, $cryptor);

        $server = Mockery::mock('Quantum\Environment\Server');

        $server->shouldReceive('method')->andReturn('GET');

        $server->shouldReceive('method')->andReturn('GET');
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

    public function testIsMethod()
    {
        $request = new Request();

        $request->create('GET', '/');

        $this->assertTrue($request->isMethod('GET'));

        $this->assertTrue($request->isMethod('get'));

        $this->assertFalse($request->isMethod('POST'));

        $request->setMethod('POST');

        $this->assertTrue($request->isMethod('POST'));

        $this->assertTrue($request->isMethod('post'));
    }

    public function testSetGetProtocol()
    {
        $request = new Request();

        $request->create('GET', 'https://test.com');

        $this->assertEquals('https', $request->getProtocol());

        $request->setProtocol('http');

        $this->assertEquals('http', $request->getProtocol());
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

        $request->create('POST', '/', ['content' => '<h1>Big text</h1>']);

        $this->assertEquals('Big text', $request->get('content'));

        $this->assertEquals('<h1>Big text</h1>', $request->get('content', null, true));

        $request->create('POST', '/', ['content' => ['status' => 'ok', 'message' => '<h1>Big text</h1>']]);

        $content = $request->get('content');

        $this->assertEquals('Big text', $content['message']);

        $content = $request->get('content', null, true);

        $this->assertEquals('<h1>Big text</h1>', $content['message']);
    }

    public function testRequestAll()
    {
        $request = new Request();

        $this->assertEmpty($request->all());

        $file = [
            'image' => [
                'size' => 500,
                'name' => 'foo.jpg',
                'tmp_name' => __FILE__ . 'php8fe1.tmp',
                'type' => 'image/jpg',
                'error' => 0,
            ],
        ];

        $request->create('POST', '/upload', ['name' => 'John'], $file);

        $request->set('name', 'John');

        $this->assertNotEmpty($request->all());

        $this->assertIsArray($request->all());
    }

    public function testHasGetFile()
    {
        $request = new Request();

        $file = [
            'image' => [
                'size' => 500,
                'name' => 'foo.jpg',
                'tmp_name' => '/tmp/php8fe2.tmp',
                'type' => 'image/jpg',
                'error' => 0,
            ],
        ];

        $request->create('POST', '/upload', null, $file);

        $this->assertTrue($request->hasFile('image'));

        $this->assertInstanceOf(File::class, $request->getFile('image'));

        $fileWithError = [
            'image' => [
                'size' => 500,
                'name' => 'foo.jpg',
                'tmp_name' => '/tmp/php8fe2.tmp',
                'type' => 'image/jpg',
                'error' => 4,
            ],
        ];

        $request->create('POST', '/upload', null, $fileWithError);

        $this->assertFalse($request->hasFile('image'));

        $this->expectException(FileUploadException::class);

        $this->expectExceptionMessage('Cannot find uploaded file identified by key `image`');

        $request->getFile('image');

    }

    public function testGetMultipleFiles()
    {
        $request = new Request();

        $this->assertFalse($request->hasFile('image'));

        $files = [
            'image' => [
                'size' => [500, 800],
                'name' => ['foo.jpg', 'bar.png'],
                'tmp_name' => ['/tmp/php8fe2.tmp', '/tmp/php8fe3.tmp'],
                'type' => ['image/jpg', 'image/png'],
                'error' => [0, 0],
            ],
        ];

        $request->create('POST', '/upload', null, $files);

        $this->assertTrue($request->hasFile('image'));

        $image = $request->getFile('image');

        $this->assertIsArray($image);

        $this->assertInstanceOf(File::class, $image[0]);

        $this->assertEquals('foo.jpg', $image[0]->getNameWithExtension());

        $this->assertEquals('bar.png', $image[1]->getNameWithExtension());
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

    public function testSetGetQueryParam()
    {
        $request = new Request();

        $request->setQueryParam('name', 'John');

        $request->setQueryParam('age', 36);

        $this->assertEquals('John', $request->getQueryParam('name'));

        $this->assertEquals(36, $request->getQueryParam('age'));

        $this->assertEquals(null, $request->getQueryParam('otherKey'));

        $this->assertEquals('name=John&age=36', $request->getQuery());

        $request->setQuery('phone=055090607&email=test@test.com');

        $this->assertEquals('test@test.com', $request->getQueryParam('email'));

    }

}
