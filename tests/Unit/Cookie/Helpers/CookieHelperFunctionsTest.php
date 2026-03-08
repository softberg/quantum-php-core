<?php

namespace Quantum\Tests\Unit\Cookie\Helpers;

use Quantum\Tests\Unit\AppTestCase;
use Quantum\Cookie\Cookie;

class CookieHelperFunctionsTest extends AppTestCase
{
    public function testCookieHelper()
    {
        $this->assertInstanceOf(Cookie::class, cookie());
    }

    public function testCookieMethodsViaHelper()
    {
        $this->assertFalse(cookie()->has('test'));

        cookie()->set('test', 'Testing');

        $this->assertTrue(cookie()->has('test'));

        $this->assertEquals('Testing', cookie()->get('test'));

        cookie()->delete('test');

        $this->assertFalse(cookie()->has('test'));
    }
}
