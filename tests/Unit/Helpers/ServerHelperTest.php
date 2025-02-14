<?php

namespace Quantum\Tests\Unit\Helpers;

use Quantum\Tests\Unit\AppTestCase;
use Quantum\Environment\Server;

class ServerHelperTest extends AppTestCase
{

    public function setUp(): void
    {
        parent::setUp();

        $this->setPrivateProperty(Server::class, 'instance', null);
    }

    public function testGetUserIpFromClientIp()
    {
        $_SERVER['HTTP_CLIENT_IP'] = '192.168.1.1';
        $_SERVER['HTTP_X_FORWARDED_FOR'] = null;
        $_SERVER['REMOTE_ADDR'] = null;

        $this->assertEquals('192.168.1.1', get_user_ip());
    }

    public function testGetUserIpFromXForwardedFor()
    {
        $_SERVER['HTTP_CLIENT_IP'] = null;
        $_SERVER['HTTP_X_FORWARDED_FOR'] = '203.0.113.5';
        $_SERVER['REMOTE_ADDR'] = null;

        $this->assertEquals('203.0.113.5', get_user_ip());
    }

    public function testGetUserIpFromRemoteAddr()
    {
        $_SERVER['HTTP_CLIENT_IP'] = null;
        $_SERVER['HTTP_X_FORWARDED_FOR'] = null;
        $_SERVER['REMOTE_ADDR'] = '198.51.100.1';

        $this->assertEquals('198.51.100.1', get_user_ip());
    }

    public function testGetAllHeaders()
    {
        $_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0';
        $_SERVER['HTTP_ACCEPT'] = 'text/html';
        $_SERVER['HTTP_X_CUSTOM_HEADER'] = 'CustomValue';
        $_SERVER['SERVER_NAME'] = 'example.com';

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