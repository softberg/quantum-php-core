<?php

namespace Quantum\Tests\Unit\Cookie;

use Quantum\Tests\Unit\AppTestCase;
use Quantum\Cookie\Cookie;

class CookieTest extends AppTestCase
{
    private Cookie $cookie;

    private array $storage = [];

    public function setUp(): void
    {
        parent::setUp();

        $this->cookie = new Cookie($this->storage);
    }

    public function tearDown(): void
    {
        $this->cookie->flush();
        parent::tearDown();
    }

    public function testCookieConstructor(): void
    {
        $this->assertInstanceOf(Cookie::class, $this->cookie);
    }

    public function testCookieAll(): void
    {
        $this->assertEmpty($this->cookie->all());

        $this->cookie->set('test', 'Test data');

        $this->cookie->set('user', ['username' => 'test@unit.com']);

        $this->assertNotEmpty($this->cookie->all());

        $this->assertIsArray($this->cookie->all());

        $this->assertArrayHasKey('test', $this->cookie->all());
    }

    public function testCookieGetSetHasDelete(): void
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

    public function testCookieFlush(): void
    {
        $this->cookie->set('test', 'Test data');

        $this->assertNotEmpty($this->cookie->all());

        $this->cookie->flush();

        $this->assertEmpty($this->cookie->all());
    }

}
