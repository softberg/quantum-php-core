<?php

namespace Quantum\Tests\Unit\Libraries\Mailer\Adapters;

use Quantum\Libraries\Storage\Factories\FileSystemFactory;
use Quantum\Libraries\Mailer\Contracts\MailerInterface;
use Quantum\Libraries\Mailer\Adapters\MandrillAdapter;
use Quantum\Tests\Unit\AppTestCase;

class MandrillAdapterTest extends AppTestCase
{

    private $adapter;

    public function setUp(): void
    {
        parent::setUp();

        $this->adapter = new MandrillAdapter(['api_key' => 'xxx111222333']);
    }

    public function tearDown(): void
    {
        parent::tearDown();

        $fs = FileSystemFactory::get();

        $emailFile = base_dir() . DS . 'shared' . DS . 'emails' . DS . $this->adapter->getMessageId() . '.eml';

        if($fs->exists($emailFile)) {
            $fs->remove($emailFile);
        }
    }

    public function testMandrillAdapterInstance()
    {
        $this->assertInstanceOf(MandrillAdapter::class, $this->adapter);

        $this->assertInstanceOf(MailerInterface::class, $this->adapter);
    }

    public function testMandrillAdapterSend()
    {
        $this->adapter->setFrom('john@hotmail.com', 'John Doe');

        $this->adapter->setAddress('benny@gmail.com', 'Benny');

        $this->adapter->setSubject('Lorem');

        $this->adapter->setBody('Lorem ipsum dolor sit amet');

        $this->assertTrue($this->adapter->send());
    }
}