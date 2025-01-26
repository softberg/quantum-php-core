<?php

namespace Quantum\Tests\Libraries\Cookie\Helpers;

use Quantum\Libraries\Cookie\Cookie;
use Quantum\Tests\AppTestCase;

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