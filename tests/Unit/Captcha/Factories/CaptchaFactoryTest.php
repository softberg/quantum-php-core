<?php

namespace Quantum\Tests\Unit\Captcha\Factories;

use Quantum\Captcha\Exceptions\CaptchaException;
use Quantum\Captcha\Adapters\RecaptchaAdapter;
use Quantum\Captcha\Adapters\HcaptchaAdapter;
use Quantum\Captcha\Factories\CaptchaFactory;
use Quantum\Tests\Unit\AppTestCase;
use Quantum\Captcha\Captcha;

class CaptchaFactoryTest extends AppTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->setPrivateProperty(CaptchaFactory::class, 'instances', []);
    }

    public function testCaptchaFactoryInstance()
    {
        $captcha = CaptchaFactory::get();

        $this->assertInstanceOf(Captcha::class, $captcha);
    }

    public function testCacheFactoryDefaultAdapter()
    {
        $captcha = CaptchaFactory::get();

        $this->assertInstanceOf(RecaptchaAdapter::class, $captcha->getAdapter());
    }

    public function testCacheFactoryRecaptchaAdapter()
    {
        $captcha = CaptchaFactory::get(Captcha::RECAPTCHA);

        $this->assertInstanceOf(RecaptchaAdapter::class, $captcha->getAdapter());
    }

    public function testCacheFactoryHcaptchaAdapter()
    {
        $captcha = CaptchaFactory::get(Captcha::HCAPTCHA);

        $this->assertInstanceOf(HcaptchaAdapter::class, $captcha->getAdapter());
    }

    public function testCacheFactoryInvalidTypeAdapter()
    {
        $this->expectException(CaptchaException::class);

        $this->expectExceptionMessage('The adapter `invalid_type` is not supported');

        CaptchaFactory::get('invalid_type');
    }

    public function testAuthFactoryReturnsSameInstance()
    {
        $captcha1 = CaptchaFactory::get(Captcha::RECAPTCHA);
        $captcha2 = CaptchaFactory::get(Captcha::RECAPTCHA);

        $this->assertSame($captcha1, $captcha2);
    }
}
