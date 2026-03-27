<?php

namespace Quantum\Tests\Unit\Mailer\Helpers;

use Quantum\Mailer\Adapters\SendinblueAdapter;
use Quantum\Mailer\Adapters\SendgridAdapter;
use Quantum\Mailer\Adapters\MandrillAdapter;
use Quantum\Mailer\Adapters\MailgunAdapter;
use Quantum\Mailer\Adapters\ResendAdapter;
use Quantum\Mailer\Adapters\SmtpAdapter;
use Quantum\Mailer\Enums\MailerType;
use Quantum\Tests\Unit\AppTestCase;
use Quantum\Mailer\Mailer;

class MailerHelperFunctionsTest extends AppTestCase
{
    public function testMailerHelperGetDefaultAdapter(): void
    {
        $mailer = mailer();

        $this->assertInstanceOf(Mailer::class, $mailer);

        $this->assertInstanceOf(SmtpAdapter::class, $mailer->getAdapter());
    }

    public function testMailerHelperGetSmtpAdapter(): void
    {
        $mailer = mailer(MailerType::SMTP);

        $this->assertInstanceOf(Mailer::class, $mailer);

        $this->assertInstanceOf(SmtpAdapter::class, $mailer->getAdapter());
    }

    public function testMailerHelperGetMailgunAdapter(): void
    {
        $mailer = mailer(MailerType::MAILGUN);

        $this->assertInstanceOf(Mailer::class, $mailer);

        $this->assertInstanceOf(MailgunAdapter::class, $mailer->getAdapter());
    }

    public function testMailerHelperGetMandrillAdapter(): void
    {
        $mailer = mailer(MailerType::MANDRILL);

        $this->assertInstanceOf(Mailer::class, $mailer);

        $this->assertInstanceOf(MandrillAdapter::class, $mailer->getAdapter());
    }

    public function testMailerHelperGetSendgridAdapter(): void
    {
        $mailer = mailer(MailerType::SENDGRID);

        $this->assertInstanceOf(Mailer::class, $mailer);

        $this->assertInstanceOf(SendgridAdapter::class, $mailer->getAdapter());
    }

    public function testMailerHelperGetSendinblueAdapter(): void
    {
        $mailer = mailer(MailerType::SENDINBLUE);

        $this->assertInstanceOf(Mailer::class, $mailer);

        $this->assertInstanceOf(SendinblueAdapter::class, $mailer->getAdapter());
    }

    public function testMailerHelperGetResendAdapter(): void
    {
        $mailer = mailer(MailerType::RESEND);

        $this->assertInstanceOf(Mailer::class, $mailer);

        $this->assertInstanceOf(ResendAdapter::class, $mailer->getAdapter());
    }
}
