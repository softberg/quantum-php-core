<?php

namespace Quantum\Tests\Libraries\Mailer;

use Quantum\Libraries\Mailer\Adapters\SmtpAdapter;
use Quantum\Libraries\Mailer\MailerInterface;
use Quantum\Libraries\Mailer\MailerManager;
use Quantum\Libraries\Storage\FileSystem;
use Quantum\Libraries\Mailer\MailTrap;
use Quantum\Tests\AppTestCase;
use Quantum\Di\Di;

class MailerManagerTest extends AppTestCase
{

    private $mailer;

    public function setUp(): void
    {
        parent::setUp();

        $this->mailer = MailerManager::getHandler();
    }

    public function tearDown(): void
    {
        unset($this->mailer);
    }

    public function testMailerInstance()
    {
        $this->assertInstanceOf(SmtpAdapter::class, $this->mailer);

        $this->assertInstanceOf(MailerInterface::class, $this->mailer);
    }

    public function testMailerAdapterMethodCall()
    {
        config()->set('mailer.mail_trap', true);

        $this->mailer
            ->setFrom('johndoe@mail.com', 'John Doe')
            ->setAddress('ban@mail.com', 'Ban Doe')
            ->setSubject('Hello')
            ->setBody('Hello everyone')
            ->send();

        $messageId = $this->mailer->getMessageId();

        $message = MailTrap::getInstance()->parseMessage($messageId);

        $this->assertEquals('John Doe <johndoe@mail.com>', $message->getParsedFromAddress());

        $this->assertEquals('Ban Doe <ban@mail.com>', $message->getParsedToAddresses()[0]);

        $this->assertEquals('Hello', $message->getParsedSubject());

        $this->assertStringContainsString('Hello everyone', $message->getParsedBody());

        Di::get(FileSystem::class)->remove(base_dir() . DS . 'shared' . DS . 'emails' . DS . $messageId . '.eml');
    }

}
