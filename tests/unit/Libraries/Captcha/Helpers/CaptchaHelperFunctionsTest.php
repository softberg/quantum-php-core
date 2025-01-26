<?php

namespace Quantum\Tests\Libraries\Captcha\Helpers;

use Quantum\Libraries\Captcha\Captcha;
use Quantum\Tests\AppTestCase;

class CaptchaHelperFunctionsTest extends AppTestCase
{

    public function testCaptchaHelper()
    {
        $this->assertInstanceOf(Captcha::class, captcha());
    }
}