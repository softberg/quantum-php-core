<?php

namespace Quantum\Tests\Unit\Libraries\Mailer\Helpers;

use Quantum\Libraries\Mailer\Mailer;
use Quantum\Tests\Unit\AppTestCase;

class MailerHelperFunctionsTest extends AppTestCase
{
    public function testMailerHelper()
    {
        $this->assertInstanceOf(Mailer::class, mailer());
    }
}