<?php

namespace Quantum\Tests\Unit\Libraries\Mailer\Adapters;

use Quantum\Tests\Unit\Libraries\Mailer\MailerTestCase;
use Quantum\Libraries\Mailer\Contracts\MailerInterface;
use Quantum\Libraries\Mailer\Adapters\SmtpAdapter;
use Quantum\Loader\Setup;

class SmtpAdapterTest extends MailerTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        if (!config()->has('mailer')) {
            config()->import(new Setup('config', 'mailer'));
        }

        $this->adapter = new SmtpAdapter(config()->get('mailer.smtp'));
    }

    public function testSmtpAdapterInstance()
    {
        $this->assertInstanceOf(SmtpAdapter::class, $this->adapter);

        $this->assertInstanceOf(MailerInterface::class, $this->adapter);
    }

    public function testSmtpAdapterSetGetReplays()
    {
        $this->adapter->setReplay('jonny@hotmail.com', 'Jonny')
            ->setReplay('jane@gmail.com');

        $replays = $this->adapter->getReplays();

        $this->assertIsArray($replays);

        $this->assertEquals('jonny@hotmail.com', $replays[0]['email']);

        $this->assertEquals('Jonny', $replays[0]['name']);

        $this->assertEquals('jane@gmail.com', $replays[1]['email']);

        $this->assertNull($replays[1]['name']);
    }

    public function testSmtpAdapterSetGetCCs()
    {
        $this->adapter->setCC('jonny@hotmail.com', 'Jonny')->setCC('jane@gmail.com');

        $CCs = $this->adapter->getCCs();

        $this->assertIsArray($CCs);

        $this->assertIsArray($CCs[0]);

        $this->assertEquals('jonny@hotmail.com', $CCs[0]['email']);

        $this->assertEquals('Jonny', $CCs[0]['name']);

    }

    public function testSmtpAdapterSetGetBCCs()
    {
        $this->adapter->setBCC('jonny@hotmail.com', 'Jonny')->setBCC('jane@gmail.com');

        $BCCs = $this->adapter->getBCCs();

        $this->assertIsArray($BCCs);

        $this->assertIsArray($BCCs[0]);

        $this->assertEquals('jonny@hotmail.com', $BCCs[0]['email']);

        $this->assertEquals('Jonny', $BCCs[0]['name']);
    }

    public function testSmtpAdapterSetGetAttachments()
    {
        $this->adapter->setAttachment(base_dir() . DS . 'php8fe1.tmp');

        $attachments = $this->adapter->getAttachments();

        $this->assertIsArray($attachments);

        $this->assertEquals(base_dir() . DS . 'php8fe1.tmp', current($attachments));
    }

    public function testSmtpAdapterSetGetStringAttachments()
    {
        $this->adapter->setStringAttachment('content of the document', 'document.txt');

        $attachments = $this->adapter->getStringAttachments();

        $this->assertIsArray($attachments);

        $this->assertEquals('content of the document', current($attachments)['content']);

        $this->assertEquals('document.txt', current($attachments)['filename']);
    }

    public function testSmtpAdapterSend()
    {
        $this->adapter->setFrom('john@hotmail.com', 'John Doe');

        $this->adapter->setAddress('benny@gmail.com', 'Benny');

        $this->adapter->setSubject('Lorem');

        $this->adapter->setBody('Lorem ipsum dolor sit amet');

        $this->assertTrue($this->adapter->send());
    }
}
