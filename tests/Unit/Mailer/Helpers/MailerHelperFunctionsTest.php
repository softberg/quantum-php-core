<?php

namespace Quantum\Tests\Unit\Mailer\Helpers;

use Quantum\Mailer\Adapters\SendinblueAdapter;
use Quantum\Mailer\Adapters\SendgridAdapter;
use Quantum\Mailer\Adapters\MandrillAdapter;
use Quantum\Mailer\Adapters\MailgunAdapter;
use Quantum\Mailer\Adapters\SmtpAdapter;
use Quantum\Tests\Unit\AppTestCase;
use Quantum\Mailer\Mailer;

class MailerHelperFunctionsTest extends AppTestCase
{
    public function testMailerHelperGetDefaultAdapter()
    {
        $mailer = mailer();

        $this->assertInstanceOf(Mailer::class, $mailer);

        $this->assertInstanceOf(SmtpAdapter::class, $mailer->getAdapter());
    }

    public function testMailerHelperGetSmtpAdapter()
    {
        $mailer = mailer(Mailer::SMTP);

        $this->assertInstanceOf(Mailer::class, $mailer);

        $this->assertInstanceOf(SmtpAdapter::class, $mailer->getAdapter());
    }

    public function testMailerHelperGetMailgunAdapter()
    {
        $mailer = mailer(Mailer::MAILGUN);

        $this->assertInstanceOf(Mailer::class, $mailer);

        $this->assertInstanceOf(MailgunAdapter::class, $mailer->getAdapter());
    }

    public function testMailerHelperGetMandrillAdapter()
    {
        $mailer = mailer(Mailer::MANDRILL);

        $this->assertInstanceOf(Mailer::class, $mailer);

        $this->assertInstanceOf(MandrillAdapter::class, $mailer->getAdapter());
    }

    public function testMailerHelperGetSendgridAdapter()
    {
        $mailer = mailer(Mailer::SENDGRID);

        $this->assertInstanceOf(Mailer::class, $mailer);

        $this->assertInstanceOf(SendgridAdapter::class, $mailer->getAdapter());
    }

    public function testMailerHelperGetSendinblueAdapter()
    {
        $mailer = mailer(Mailer::SENDINBLUE);

        $this->assertInstanceOf(Mailer::class, $mailer);

        $this->assertInstanceOf(SendinblueAdapter::class, $mailer->getAdapter());
    }
}
