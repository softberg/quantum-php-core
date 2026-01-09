<?php

namespace Quantum\Tests\Unit\Libraries\Captcha\Helpers;

use Quantum\Libraries\Captcha\Adapters\RecaptchaAdapter;
use Quantum\Libraries\Captcha\Adapters\HcaptchaAdapter;
use Quantum\Libraries\Captcha\Captcha;
use Quantum\Tests\Unit\AppTestCase;

class CaptchaHelperFunctionsTest extends AppTestCase
{
    public function testCaptchaHelperGetDefaultCaptchaAdapter()
    {
        $this->assertInstanceOf(Captcha::class, captcha());

        $this->assertInstanceOf(RecaptchaAdapter::class, captcha()->getAdapter());
    }

    public function testCaptchaHelperGetRecaptchaAdapter()
    {
        $this->assertInstanceOf(Captcha::class, captcha(Captcha::RECAPTCHA));

        $this->assertInstanceOf(RecaptchaAdapter::class, captcha(Captcha::RECAPTCHA)->getAdapter());
    }

    public function testCaptchaHelperGetHcaptchaAdapter()
    {
        $this->assertInstanceOf(Captcha::class, captcha(Captcha::HCAPTCHA));

        $this->assertInstanceOf(HcaptchaAdapter::class, captcha(Captcha::HCAPTCHA)->getAdapter());
    }
}
