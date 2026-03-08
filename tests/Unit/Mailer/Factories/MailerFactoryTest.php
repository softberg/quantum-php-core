<?php

namespace Quantum\Tests\Unit\Mailer\Factories;

use Quantum\Mailer\Exceptions\MailerException;
use Quantum\Mailer\Adapters\SendinblueAdapter;
use Quantum\Mailer\Adapters\MandrillAdapter;
use Quantum\Mailer\Adapters\SendgridAdapter;
use Quantum\Mailer\Adapters\MailgunAdapter;
use Quantum\Mailer\Factories\MailerFactory;
use Quantum\Mailer\Adapters\SmtpAdapter;
use Quantum\Tests\Unit\AppTestCase;
use Quantum\Mailer\Mailer;
use Quantum\Loader\Setup;

class MailerFactoryTest extends AppTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->setPrivateProperty(MailerFactory::class, 'instances', []);

        if (!config()->has('mailer')) {
            config()->import(new Setup('config', 'mailer'));
        }
    }

    public function testMailerFactoryInstance()
    {
        $mailer = MailerFactory::get();

        $this->assertInstanceOf(Mailer::class, $mailer);
    }

    public function testMailerFactoryGetDefaultAdapter()
    {
        $mailer = MailerFactory::get();

        $this->assertInstanceOf(SmtpAdapter::class, $mailer->getAdapter());
    }

    public function testMailerFactoryGetSmtpAdapter()
    {
        $mailer = MailerFactory::get(Mailer::SMTP);

        $this->assertInstanceOf(SmtpAdapter::class, $mailer->getAdapter());
    }

    public function testMailerFactoryGetMailgunAdapter()
    {
        $mailer = MailerFactory::get(Mailer::MAILGUN);

        $this->assertInstanceOf(MailgunAdapter::class, $mailer->getAdapter());
    }

    public function testMailerFactoryGetMandrillAdapter()
    {
        $mailer = MailerFactory::get(Mailer::MANDRILL);

        $this->assertInstanceOf(MandrillAdapter::class, $mailer->getAdapter());
    }

    public function testMailerFactoryGetSendgridAdapter()
    {
        $mailer = MailerFactory::get(Mailer::SENDGRID);

        $this->assertInstanceOf(SendgridAdapter::class, $mailer->getAdapter());
    }

    public function testMailerFactoryGetSendinblueAdapter()
    {
        $mailer = MailerFactory::get(Mailer::SENDINBLUE);

        $this->assertInstanceOf(SendinblueAdapter::class, $mailer->getAdapter());
    }

    public function testMailerFactoryGetInvalidTypeAdapter()
    {
        $this->expectException(MailerException::class);

        $this->expectExceptionMessage('The adapter `invalid_type` is not supported');

        MailerFactory::get('invalid_type');
    }

    public function testMailerFactoryReturnsSameInstance()
    {
        $mailer1 = MailerFactory::get(Mailer::SMTP);
        $mailer2 = MailerFactory::get(Mailer::SMTP);

        $this->assertSame($mailer1, $mailer2);
    }
}
