<?php

namespace Quantum\Tests\Unit\Environment\Helpers;

use Quantum\Tests\Unit\AppTestCase;

class ServerHelperTest extends AppTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        server()->flush();
    }

    public function testGetUserIpFromClientIp(): void
    {
        server()->set('HTTP_CLIENT_IP', '192.168.1.1');

        $this->assertEquals('192.168.1.1', get_user_ip());
    }

    public function testGetUserIpFromXForwardedFor(): void
    {
        server()->set('HTTP_X_FORWARDED_FOR', '203.0.113.5');

        $this->assertEquals('203.0.113.5', get_user_ip());
    }

    public function testGetUserIpFromRemoteAddr(): void
    {
        server()->set('REMOTE_ADDR', '198.51.100.1');

        $this->assertEquals('198.51.100.1', get_user_ip());
    }

    public function testGetAllHeaders(): void
    {
        server()->set('HTTP_USER_AGENT', 'Mozilla/5.0');
        server()->set('HTTP_ACCEPT', 'text/html');
        server()->set('HTTP_X_CUSTOM_HEADER', 'CustomValue');
        server()->set('SERVER_NAME', 'example.com');

        $headers = getallheaders();

        $this->assertArrayHasKey('user-agent', $headers);
        $this->assertArrayHasKey('accept', $headers);
        $this->assertArrayHasKey('x-custom-header', $headers);

        $this->assertEquals('Mozilla/5.0', $headers['user-agent']);
        $this->assertEquals('text/html', $headers['accept']);
        $this->assertEquals('CustomValue', $headers['x-custom-header']);

        $this->assertArrayNotHasKey('SERVER_NAME', $headers);
    }

}
