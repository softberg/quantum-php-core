<?php

namespace Quantum\Tests\Unit\Libraries\Mailer\Adapters;

use Quantum\Libraries\Mailer\Adapters\SendinblueAdapter;
use Quantum\Tests\Unit\Libraries\Mailer\MailerTestCase;
use Quantum\Libraries\Mailer\Contracts\MailerInterface;

class SendinblueAdapterTest extends MailerTestCase
{

    protected $adapter;

    public function setUp(): void
    {
        parent::setUp();

        $this->adapter = new SendinblueAdapter(['api_key' => 'xxx111222333']);
    }

    public function testSendinblueAdapterInstance()
    {
        $this->assertInstanceOf(SendinblueAdapter::class, $this->adapter);

        $this->assertInstanceOf(MailerInterface::class, $this->adapter);
    }

    public function testSendinblueAdapterSend()
    {
        $this->adapter->setFrom('john@hotmail.com', 'John Doe');

        $this->adapter->setAddress('benny@gmail.com', 'Benny');

        $this->adapter->setSubject('Lorem');

        $this->adapter->setBody('Lorem ipsum dolor sit amet');

        $this->assertTrue($this->adapter->send());
    }
}