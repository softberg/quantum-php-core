<?php

namespace Quantum\Tests\Unit\Libraries\Mailer;

use Quantum\Libraries\Mailer\Exceptions\MailerException;
use Quantum\Libraries\Mailer\Contracts\MailerInterface;
use Quantum\Libraries\Mailer\Adapters\SmtpAdapter;
use Quantum\Libraries\Mailer\Mailer;
use Quantum\Tests\Unit\AppTestCase;
use Quantum\Loader\Setup;

class MailerTest extends AppTestCase
{

    public function setUp(): void
    {
        parent::setUp();

        if (!config()->has('mailer')) {
            config()->import(new Setup('config', 'mailer'));
        }
    }

    public function testMailerGetAdapter()
    {
        $mailer = new Mailer(new SmtpAdapter(config()->get('mailer.smtp')));

        $this->assertInstanceOf(SmtpAdapter::class, $mailer->getAdapter());

        $this->assertInstanceOf(MailerInterface::class, $mailer->getAdapter());
    }

    public function testMailerCallingValidMethod()
    {
        $mailer = new Mailer(new SmtpAdapter(config()->get('mailer.smtp')));

        $mailer->setSubject('Welcome');

        $this->assertEquals('Welcome', $mailer->getSubject());
    }

    public function testMailerCallingInvalidMethod()
    {
        $mailer = new Mailer(new SmtpAdapter(config()->get('mailer.smtp')));

        $this->expectException(MailerException::class);

        $this->expectExceptionMessage('The method `callingInvalidMethod` is not supported for `'. SmtpAdapter::class .'`');

        $mailer->callingInvalidMethod();
    }
}