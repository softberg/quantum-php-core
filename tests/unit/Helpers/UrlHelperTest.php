<?php

namespace Quantum\Tests\Helpers;

use Quantum\Exceptions\StopExecutionException;
use Quantum\Libraries\Session\Session;
use Quantum\Tests\AppTestCase;
use Quantum\Http\Response;
use Quantum\Router\Router;
use Quantum\Http\Request;

class UrlHelperTest extends AppTestCase
{

    private $request;
    private $response;

    public function setUp(): void
    {
        parent::setUp();

        $this->request = new Request();

        Response::init();

        $this->response = new Response();
    }

    public function tearDown(): void
    {
        Request::flush();
    }

    public function testBaseUrlWithoutModulePrefix()
    {
        $this->request->create('GET', 'https://example.com');

        $this->assertEquals('https://example.com', base_url());
    }

    public function testBaseUrlWithModulePrefix()
    {
        $router = new Router($this->request, new Response());

        Router::setRoutes([
            [
                "route" => "signin",
                "method" => "GET",
                "controller" => "AdminController",
                "action" => "signin",
                "module" => "admin",
                'prefix' => 'admin'
            ]
        ]);

        $this->request->create('GET', 'https://testdomain.com/signin');

        $router->findRoute();

        $baseUrl = base_url(true);

        $this->assertEquals('https://testdomain.com/admin', $baseUrl);
    }

    public function testCurrentUrl()
    {
        $this->request->create('GET', 'http://test.com/post/12');

        $this->assertEquals('http://test.com/post/12', current_url());

        $this->request->create('GET', 'http://test.com/user/12?firstname=John&lastname=Doe');

        $this->assertEquals('http://test.com/user/12?firstname=John&lastname=Doe', current_url());

        $this->request->create('GET', 'http://test.com:8080/?firstname=John&lastname=Doe');

        $this->assertEquals('http://test.com:8080/?firstname=John&lastname=Doe', current_url());
    }

    public function testRedirecting()
    {
        $this->assertFalse($this->response->hasHeader('Location'));

        try {
            redirect('/home');
        } catch (StopExecutionException $e) {

        }

        $this->assertTrue($this->response->hasHeader('Location'));

        $this->assertEquals('/home', $this->response->getHeader('Location'));
    }

    public function testRedirectWithOldData()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $sessionData = [];

        $this->session = Session::getInstance($sessionData);

        $this->request->create('POST', '/', ['firstname' => 'Josh', 'lastname' => 'Doe']);

        try {
            redirectWith('/signup', $this->request->all());
        } catch (StopExecutionException $e) {

        }

        $this->assertTrue($this->response->hasHeader('Location'));

        $this->assertEquals('/signup', $this->response->getHeader('Location'));

        $this->assertEquals('Josh', old('firstname'));

        $this->assertEquals('Doe', old('lastname'));
    }

}