<?php

namespace Http\Traits\Request;

use Quantum\Tests\Unit\AppTestCase;
use Quantum\Http\Request;

class HttpRequestBodyTest extends AppTestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function tearDown(): void
    {
        Request::flush();
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
}
