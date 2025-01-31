<?php

namespace Quantum\Tests\Libraries\Mailer\Helpers;

use Quantum\Libraries\Mailer\Mailer;
use Quantum\Tests\AppTestCase;

class MailerHelperFunctionsTest extends AppTestCase
{
    public function testMailerHelper()
    {
        $this->assertInstanceOf(Mailer::class, mailer());
    }
}