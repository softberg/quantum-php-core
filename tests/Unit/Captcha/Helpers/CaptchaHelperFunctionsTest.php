<?php

namespace Quantum\Tests\Unit\Captcha\Helpers;

use Quantum\Captcha\Adapters\RecaptchaAdapter;
use Quantum\Captcha\Adapters\HcaptchaAdapter;
use Quantum\Captcha\Enums\CaptchaType;
use Quantum\Tests\Unit\AppTestCase;
use Quantum\Captcha\Captcha;

class CaptchaHelperFunctionsTest extends AppTestCase
{
    public function testCaptchaHelperGetDefaultCaptchaAdapter(): void
    {
        $this->assertInstanceOf(Captcha::class, captcha());

        $this->assertInstanceOf(RecaptchaAdapter::class, captcha()->getAdapter());
    }

    public function testCaptchaHelperGetRecaptchaAdapter(): void
    {
        $this->assertInstanceOf(Captcha::class, captcha(CaptchaType::RECAPTCHA));

        $this->assertInstanceOf(RecaptchaAdapter::class, captcha(CaptchaType::RECAPTCHA)->getAdapter());
    }

    public function testCaptchaHelperGetHcaptchaAdapter(): void
    {
        $this->assertInstanceOf(Captcha::class, captcha(CaptchaType::HCAPTCHA));

        $this->assertInstanceOf(HcaptchaAdapter::class, captcha(CaptchaType::HCAPTCHA)->getAdapter());
    }
}
