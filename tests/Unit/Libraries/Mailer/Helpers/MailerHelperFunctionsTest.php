<?php

namespace Quantum\Tests\Unit\Libraries\Mailer\Helpers;

use Quantum\Libraries\Mailer\Adapters\SendinblueAdapter;
use Quantum\Libraries\Mailer\Adapters\SendgridAdapter;
use Quantum\Libraries\Mailer\Adapters\MandrillAdapter;
use Quantum\Libraries\Mailer\Adapters\MailgunAdapter;
use Quantum\Libraries\Mailer\Adapters\SmtpAdapter;
use Quantum\Libraries\Mailer\Mailer;
use Quantum\Tests\Unit\AppTestCase;

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