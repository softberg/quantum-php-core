<?php

namespace Quantum\Test\Unit;

use Mockery;
use PHPUnit\Framework\TestCase;
use Quantum\Libraries\Cookie\Cookie;

class CookieTest extends TestCase
{
    private $cookieAdapter;

    private $cookie;

    private $cookieData = [
        'auth' => 'ok',
        'test' => 'good',
        'store' => 'persists'
    ];

    public function setUp(): void
    {
        $this->cookieAdapter = Mockery::mock('Quantum\Libraries\Cookie\CookieAdapter');

        $this->cookie = new Cookie($this->cookieAdapter);
    }

    public function tearDown(): void
    {
        Mockery::close();
    }

    public function testCookieGet()
    {
        $this->cookieAdapter->shouldReceive('get')
            ->with('auth')
            ->andReturnUsing(function ($key) {
                return isset($this->cookieData[$key]) ? $this->cookieData[$key] : null;
            });

        $this->assertEquals('ok', $this->cookie->get('auth'));

        $this->cookieAdapter->shouldReceive('get')
            ->with('not-exists')
            ->andReturnUsing(function ($key) {
                return isset($this->cookieData[$key]) ? $this->cookieData[$key] : null;
            });

        $this->assertNull($this->cookie->get('not-exists'));
    }

    public function testCookieGetAll()
    {
        $this->cookieAdapter->shouldReceive('all')
            ->andReturn($this->cookieData);

        $this->assertEquals($this->cookieData, $this->cookie->all());
    }

    public function testCookieHas()
    {
        $this->cookieAdapter->shouldReceive('has')
            ->with('not-exists')
            ->andReturnUsing(function ($key) {
                return isset($this->cookieData[$key]);
            });

        $this->assertFalse($this->cookie->has('not-exists'));

        $this->cookieAdapter->shouldReceive('has')
            ->with('test')
            ->andReturnUsing(function ($key) {
                return isset($this->cookieData[$key]);
            });

        $this->assertTrue($this->cookie->has('test'));
    }

    public function testCookieSet()
    {
        $this->cookieAdapter->shouldReceive('set')
            ->with('new', 'New Value', 300, $path = '/', $domain = '', false, false)
            ->andReturnUsing(function ($key, $value, $time, $path, $domain, $secure, $httponly) {
                $this->cookieData[$key] = $value;
                return true;
            });

        $this->cookieAdapter->shouldReceive('has')
            ->with('new')
            ->andReturnUsing(function ($key) {
                return isset($this->cookieData[$key]);
            });

        $this->cookieAdapter->shouldReceive('get')
            ->with('new')
            ->andReturnUsing(function ($key) {
                return isset($this->cookieData[$key]) ? $this->cookieData[$key] : null;
            });

        $this->assertTrue($this->cookie->set('new', 'New Value', 300, $path = '/', $domain = '', false, false));

        $this->assertTrue($this->cookie->has('new'));

        $this->assertEquals('New Value', $this->cookie->get('new'));
    }

    public function testCookieDelete()
    {
        $this->cookieAdapter->shouldReceive('has')
            ->with('test')
            ->andReturnUsing(function ($key) {
                return isset($this->cookieData[$key]);
            });

        $this->assertTrue($this->cookie->has('test'));

        $this->cookieAdapter->shouldReceive('delete')
            ->with('test', '/')
            ->andReturnUsing(function ($key, $path) {
                unset($this->cookieData[$key]);
                return true;
            });

        $this->cookie->delete('test', '/');

        $this->assertFalse($this->cookie->has('test'));

    }

    public function testCookieFlush()
    {
        $this->cookieAdapter->shouldReceive('all')
            ->andReturnUsing(function () {
                return $this->cookieData;
            });

        $this->cookieAdapter->shouldReceive('flush')
            ->andReturnUsing(function () {
                $this->cookieData = null;
            });

        $this->assertNotNull($this->cookie->all());

        $this->cookie->flush();

        $this->assertNull($this->cookie->all());
    }

}