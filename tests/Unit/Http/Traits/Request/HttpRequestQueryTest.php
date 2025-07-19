<?php

namespace Http\Traits\Request;

use Quantum\Tests\Unit\AppTestCase;
use Quantum\Http\Request;

class HttpRequestQueryTest extends AppTestCase
{

    public function setUp(): void
    {
        parent::setUp();
    }

    public function tearDown(): void
    {
        Request::flush();
    }

    public function testSetGetQuery()
    {
        $request = new Request();

        $request->create('GET', 'http://test.com:8080/user?firstname=john&lastname=doe');

        $this->assertEquals('firstname=john&lastname=doe', $request->getQuery());

        $request->create('GET', 'http://test.com:8080/?firstname=john&lastname=doe');

        $this->assertEquals('firstname=john&lastname=doe', $request->getQuery());

        $request->setQuery('age=30&gender=male');

        $this->assertEquals('age=30&gender=male', $request->getQuery());
    }

    public function testSetGetQueryParam()
    {
        $request = new Request();

        $request->setQueryParam('name', 'John');

        $request->setQueryParam('age', 36);

        $this->assertEquals('John', $request->getQueryParam('name'));

        $this->assertEquals(36, $request->getQueryParam('age'));

        $this->assertEquals(null, $request->getQueryParam('otherKey'));

        $this->assertEquals('name=John&age=36', $request->getQuery());

        $request->setQuery('phone=055090607&email=test@test.com');

        $this->assertEquals('test@test.com', $request->getQueryParam('email'));
    }
}