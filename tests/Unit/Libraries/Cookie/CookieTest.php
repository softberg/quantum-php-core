<?php

namespace Quantum\Tests\Unit\Libraries\Cookie;

use Quantum\Libraries\Cookie\Cookie;
use Quantum\Tests\Unit\AppTestCase;

class CookieTest extends AppTestCase
{
    private $cookie;

    private $storage = [];

    public function setUp(): void
    {
        parent::setUp();

        $this->cookie = Cookie::getInstance($this->storage);
    }

    public function tearDown(): void
    {
        $this->cookie->flush();
    }

    public function testCookieConstructor()
    {
        $this->assertInstanceOf(Cookie::class, $this->cookie);
    }

    public function testCookieAll()
    {
        $this->assertEmpty($this->cookie->all());

        $this->cookie->set('test', 'Test data');

        $this->cookie->set('user', ['username' => 'test@unit.com']);

        $this->assertNotEmpty($this->cookie->all());

        $this->assertIsArray($this->cookie->all());

        $this->assertArrayHasKey('test', $this->cookie->all());
    }

    public function testCookieGetSetHasDelete()
    {
        $this->assertNull($this->cookie->get('auth'));

        $this->assertFalse($this->cookie->has('auth'));

        $this->cookie->set('auth', 'Authenticated');

        $this->assertTrue($this->cookie->has('auth'));

        $this->assertEquals('Authenticated', $this->cookie->get('auth'));

        $this->cookie->delete('auth');

        $this->assertFalse($this->cookie->has('auth'));

        $this->assertNull($this->cookie->get('auth'));
    }

    public function testCookieFlush()
    {
        $this->cookie->set('test', 'Test data');

        $this->assertNotEmpty($this->cookie->all());

        $this->cookie->flush();

        $this->assertEmpty($this->cookie->all());
    }

}
