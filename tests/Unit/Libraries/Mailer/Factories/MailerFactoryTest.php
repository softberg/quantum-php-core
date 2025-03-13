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

        $this->setPrivateProperty(MailerFactory::class, 'instance', null);

        if (!config()->has('mailer')) {
            config()->import(new Setup('config', 'mailer'));
        }
    }

    public function testMailerFactoryInstance()
    {
        $mailer = MailerFactory::get();

        $this->assertInstanceOf(Mailer::class, $mailer);
    }

    public function testMailerFactorySmtpAdapter()
    {
        $mailer = MailerFactory::get();

        $this->assertInstanceOf(SmtpAdapter::class, $mailer->getAdapter());
    }

    public function testMailerFactoryMailgunAdapter()
    {
        config()->set('mailer.default', 'mailgun');

        $mailer = MailerFactory::get();

        $this->assertInstanceOf(MailgunAdapter::class, $mailer->getAdapter());
    }

    public function testMailerFactoryMandrillAdapter()
    {
        config()->set('mailer.default', 'mandrill');

        $mailer = MailerFactory::get();

        $this->assertInstanceOf(MandrillAdapter::class, $mailer->getAdapter());
    }

    public function testMailerFactorySendgridAdapter()
    {
        config()->set('mailer.default', 'sendgrid');

        $mailer = MailerFactory::get();

        $this->assertInstanceOf(SendgridAdapter::class, $mailer->getAdapter());
    }

    public function testMailerFactorySendinblueAdapter()
    {
        config()->set('mailer.default', 'sendinblue');

        $mailer = MailerFactory::get();

        $this->assertInstanceOf(SendinblueAdapter::class, $mailer->getAdapter());
    }

    public function testMailerFactoryInvalidTypeAdapter()
    {
        config()->set('mailer.default', 'invalid');

        $this->expectException(MailerException::class);

        $this->expectExceptionMessage('The adapter `invalid` is not supported`');

        MailerFactory::get();
    }

    public function testMailerFactoryReturnsSameInstance()
    {
        $mailer1 = MailerFactory::get();
        $mailer2 = MailerFactory::get();

        $this->assertSame($mailer1, $mailer2);
    }
}