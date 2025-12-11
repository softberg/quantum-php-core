<?php

namespace Quantum\Tests\Unit\Libraries\Mailer\Factories;

use Quantum\Libraries\Mailer\Exceptions\MailerException;
use Quantum\Libraries\Mailer\Adapters\SendinblueAdapter;
use Quantum\Libraries\Mailer\Adapters\MandrillAdapter;
use Quantum\Libraries\Mailer\Adapters\SendgridAdapter;
use Quantum\Libraries\Mailer\Adapters\MailgunAdapter;
use Quantum\Libraries\Mailer\Factories\MailerFactory;
use Quantum\Libraries\Mailer\Adapters\SmtpAdapter;
use Quantum\Libraries\Mailer\Mailer;
use Quantum\Tests\Unit\AppTestCase;
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