<?php

namespace Quantum\Tests\Helpers;

use Quantum\Libraries\Session\Session;
use Quantum\Libraries\Cookie\Cookie;
use Quantum\Tests\AppTestCase;
use Quantum\Http\Request;

class MiscHelperTest extends AppTestCase
{

    public function setUp(): void
    {
        parent::setUp();
    }

    public function testRandomNumber()
    {
        $this->assertIsInt(random_number());

        $this->assertIsInt(random_number(5));
    }

    public function testValidBase64()
    {
        $validBase64String = base64_encode('test');

        $invalidBase64String = 'abc123';

        $this->assertTrue(valid_base64($validBase64String));

        $this->assertFalse(valid_base64($invalidBase64String));
    }

    public function testMessageHelper()
    {
        $this->assertEquals('Hello John', _message('Hello {%1}', 'John'));

        $this->assertEquals('Hello John, greetings from Jenny', _message('Hello {%1}, greetings from {%2}', ['John', 'Jenny']));
    }


    public function testSlugify()
    {
        $this->assertEquals('text-with-spaces', slugify('Text with spaces'));

        $this->assertEquals('ebay-com-itm-dual-arm-tv-trkparms-aid-3d111001-26brand-3dunbranded-trksid-p2380057', slugify('ebay.com/itm/DUAL-ARM-TV/?_trkparms=aid%3D111001%26brand%3DUnbranded&_trksid=p2380057'));
    }

    public function testCryptoEncodeDecode()
    {
        $data = "test_string";
        $encoded = crypto_encode($data);

        $this->assertNotEquals($data, $encoded);

        $decoded = crypto_decode($encoded);

        $this->assertEquals($data, $decoded);

        $data = ['key' => 'value'];
        $encoded = crypto_encode($data);

        $this->assertIsString($encoded);

        $decoded = crypto_decode($encoded);
        $this->assertEquals($data, $decoded);

        $data = (object) ['key' => 'value'];
        $encoded = crypto_encode($data);

        $this->assertIsString($encoded);

        $decoded = crypto_decode($encoded);
        $this->assertEquals($data, $decoded);
    }

    public function testCsrfToken()
    {
        $request = new Request();

        $request->create('PUT', '/update', ['title' => 'Task Title', 'csrf-token' => csrf_token()]);

        $this->assertTrue(csrf()->checkToken($request));
    }

    public function testSessionHelper()
    {
        $this->assertInstanceOf(Session::class, session());

        $this->assertFalse(session()->has('test'));

        session()->set('test', 'Testing');

        $this->assertTrue(session()->has('test'));

        $this->assertEquals('Testing', session()->get('test'));
    }

    public function testCookieHelper()
    {
        $this->assertInstanceOf(Cookie::class, cookie());

        $this->assertFalse(cookie()->has('test'));

        cookie()->set('test', 'Testing');

        $this->assertTrue(cookie()->has('test'));

        $this->assertEquals('Testing', cookie()->get('test'));
    }

}