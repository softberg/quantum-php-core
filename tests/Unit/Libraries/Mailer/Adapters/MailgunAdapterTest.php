<?php

namespace Quantum\Tests\Unit\Libraries\Mailer\Adapters;

use Quantum\Tests\Unit\Libraries\Mailer\MailerTestCase;
use Quantum\Libraries\Mailer\Contracts\MailerInterface;
use Quantum\Libraries\Mailer\Adapters\MailgunAdapter;

class MailgunAdapterTest extends MailerTestCase
{

    protected $adapter;

    public function setUp(): void
    {
        parent::setUp();

        $config = [
            'api_key' => 'xxx11122233',
            'domain' => 'mailgun.example.com',
        ];

        $this->adapter = new MailgunAdapter($config);
    }

    public function testMailgunAdapterInstance()
    {
        $this->assertInstanceOf(MailgunAdapter::class, $this->adapter);

        $this->assertInstanceOf(MailerInterface::class, $this->adapter);
    }

    public function testMailgunAdapterSend()
    {
        $this->adapter->setFrom('john@hotmail.com', 'John Doe');

        $this->adapter->setAddress('benny@gmail.com', 'Benny');

        $this->adapter->setSubject('Lorem');

        $this->adapter->setBody('Lorem ipsum dolor sit amet');

        $this->assertTrue($this->adapter->send());
    }
}