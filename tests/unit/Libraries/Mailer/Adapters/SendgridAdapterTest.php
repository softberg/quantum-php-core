<?php

namespace Quantum\Tests\Libraries\Mailer\Adapters;

use Quantum\Libraries\Mailer\Adapters\SendgridAdapter;
use Quantum\Libraries\Mailer\MailerInterface;
use Quantum\Libraries\Storage\FileSystem;
use Quantum\Tests\AppTestCase;
use Quantum\Loader\Setup;
use Quantum\Di\Di;

class SendgridAdapterTest extends AppTestCase
{

    private $adapter;

    public function setUp(): void
    {
        parent::setUp();

        config()->set('base_url', '127.0.0.1');

        if (!config()->has('mailer')) {
            config()->import(new Setup('config', 'mailer'));
        }

        $this->adapter = SendgridAdapter::getInstance(config()->get('mailer.sendgrid'));
    }

    public function testSendgridAdapterInstance()
    {
        $this->assertInstanceOf(SendgridAdapter::class, $this->adapter);

        $this->assertInstanceOf(MailerInterface::class, $this->adapter);
    }

    public function testSendgridAdapterSend()
    {
        $this->adapter->setFrom('john@hotmail.com', 'John Doe');

        $this->adapter->setAddress('benny@gmail.com', 'Benny');

        $this->adapter->setSubject('Lorem');

        $this->adapter->setBody('Lorem ipsum dolor sit amet');

        $this->assertTrue($this->adapter->send());

        Di::get(FileSystem::class)->remove(base_dir() . DS . 'shared' . DS . 'emails' . DS . $this->adapter->getMessageId() . '.eml');
    }

}
