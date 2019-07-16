<?php

namespace Quantum\Test\Unit;

use PHPUnit\Framework\TestCase;
use Quantum\Libraries\Cookie\Cookie;


class CookieTest extends TestCase
{
    private $cookie;

    private $cookieData = [
        'auth' => 'b2s=', // ok
        'test' => 'Z29vZA==', // good
        'store' => 'cGVyc2lzdA==' // persist
    ];

    public function setUp(): void
    {
        $this->cookie = new Cookie($this->cookieData);
    }

    public function testCookieConstructor()
    {
        $this->assertInstanceOf('Quantum\Libraries\Cookie\Cookie', $this->cookie);
    }

    public function testCookieGet()
    {
        $this->assertEquals('ok', $this->cookie->get('auth'));

        $this->assertNull($this->cookie->get('not-exists'));
    }

    public function testCookieAll()
    {
        $this->assertEquals(array_map('base64_decode', $this->cookieData), $this->cookie->all());
    }

    public function testCookieHas()
    {
        $this->assertFalse($this->cookie->has('not-exists'));

        $this->assertTrue($this->cookie->has('test'));
    }

    public function testCookieSet()
    {
        $this->cookie->set('new', 'New Value');

        $this->assertTrue($this->cookie->has('new'));

        $this->assertEquals('New Value', $this->cookie->get('new'));
    }

    public function testCookieDelete()
    {
        $this->assertTrue($this->cookie->has('test'));

        $this->cookie->delete('test');

        $this->assertFalse($this->cookie->has('test'));
    }

    public function testCookieFlush()
    {
        $this->assertNotEmpty($this->cookie->all());

        $this->cookie->flush();

        $this->assertEmpty($this->cookie->all());
    }

}