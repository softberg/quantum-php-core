<?php

namespace Quantum\Tests\Unit\Http\Helpers;

use Quantum\Session\Factories\SessionFactory;
use Quantum\Tests\Unit\AppTestCase;
use Quantum\Router\RouteCollection;
use Quantum\Router\RouteFinder;
use Quantum\Router\Route;
use Quantum\Http\Response;
use Quantum\Http\Request;
use Quantum\Di\Di;

class HttpHelperTest extends AppTestCase
{
    public $session;
    private Request $request;
    private Response $response;

    public function setUp(): void
    {
        parent::setUp();

        $this->request = request();
        $this->response = response();
    }

    public function tearDown(): void
    {
        request()->flush();
    }

    public function testRequestHelperReturnsDiInstance(): void
    {
        $this->assertInstanceOf(Request::class, request());
        $this->assertSame(request(), request());
    }

    public function testResponseHelperReturnsDiInstance(): void
    {
        $this->assertInstanceOf(Response::class, response());
        $this->assertSame(response(), response());
    }

    public function testBaseUrlWithoutModulePrefix(): void
    {
        config()->set('app.base_url', null);

        $this->request->create('GET', 'https://example.com');

        $this->assertEquals('https://example.com', base_url());
    }

    public function testBaseUrlWithModulePrefix(): void
    {
        config()->set('app.base_url', null);

        $routeCollection = new RouteCollection();

        $route = new Route(
            ['GET'],
            '/signin',
            'AdminController',
            'signin'
        );
        $route->module('admin')->prefix('admin');

        $routeCollection->add($route);

        // Register route collection in DI
        Di::set(RouteCollection::class, $routeCollection);

        // Create route finder and find the route
        $router = new RouteFinder($routeCollection);

        $this->request->create('GET', 'https://testdomain.com/signin');

        $matchedRoute = $router->find($this->request);

        request()->setMatchedRoute($matchedRoute);

        $baseUrl = base_url(true);

        $this->assertEquals('https://testdomain.com/admin', $baseUrl);
    }

    public function testCurrentUrl(): void
    {
        $this->request->create('GET', 'http://test.com/post/12');

        $this->assertEquals('http://test.com/post/12', current_url());

        $this->request->create('GET', 'http://test.com/user/12?firstname=John&lastname=Doe');

        $this->assertEquals('http://test.com/user/12?firstname=John&lastname=Doe', current_url());

        $this->request->create('GET', 'http://test.com:8080/?firstname=John&lastname=Doe');

        $this->assertEquals('http://test.com:8080/?firstname=John&lastname=Doe', current_url());
    }

    public function testRedirecting(): void
    {
        $this->assertFalse($this->response->hasHeader('Location'));

        $redirectResponse = redirect('/home');

        $this->assertSame($this->response, $redirectResponse);

        $this->assertTrue($this->response->hasHeader('Location'));

        $this->assertEquals('/home', $this->response->getHeader('Location'));
    }

    public function testRedirectWithOldData(): void
    {
        $this->session = SessionFactory::get();

        $this->request->create('POST', '/', ['firstname' => 'Josh', 'lastname' => 'Doe']);

        $redirectResponse = redirectWith('/signup', $this->request->all());

        $this->assertSame($this->response, $redirectResponse);

        $this->assertTrue($this->response->hasHeader('Location'));

        $this->assertEquals('/signup', $this->response->getHeader('Location'));

        $this->assertEquals('Josh', old('firstname'));

        $this->assertEquals('Doe', old('lastname'));

        $this->session->flush();
    }
}
