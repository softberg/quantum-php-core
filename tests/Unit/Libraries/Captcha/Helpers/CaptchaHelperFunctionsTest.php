<?php

namespace Quantum\Tests\Unit\Libraries\Captcha\Helpers;

use Quantum\Libraries\Captcha\Captcha;
use Quantum\Tests\Unit\AppTestCase;

class CaptchaHelperFunctionsTest extends AppTestCase
{

    public function testCaptchaHelper()
    {
        $this->assertInstanceOf(Captcha::class, captcha());
    }
}