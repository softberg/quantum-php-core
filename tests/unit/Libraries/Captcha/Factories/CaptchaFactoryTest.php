<?php

namespace Quantum\Tests\Libraries\Captcha\Factories;

use Quantum\Libraries\Captcha\Exceptions\CaptchaException;
use Quantum\Libraries\Captcha\Adapters\RecaptchaAdapter;
use Quantum\Libraries\Captcha\Adapters\HcaptchaAdapter;
use Quantum\Libraries\Captcha\Factories\CaptchaFactory;
use Quantum\Libraries\Captcha\Captcha;
use Quantum\Tests\AppTestCase;
use ReflectionClass;

class CaptchaFactoryTest extends AppTestCase
{

    public function setUp(): void
    {
        parent::setUp();

        $reflection = new ReflectionClass(CaptchaFactory::class);
        $property = $reflection->getProperty('instance');
        $property->setAccessible(true);
        $property->setValue(null);
    }

    public function testCaptchaFactoryInstance()
    {
        $captcha = CaptchaFactory::get();

        $this->assertInstanceOf(Captcha::class, $captcha);
    }

    public function testCacheFactoryRecaptchaAdapter()
    {
        $captcha = CaptchaFactory::get();

        $this->assertInstanceOf(RecaptchaAdapter::class, $captcha->getAdapter());
    }

    public function testCacheFactoryHcaptchaAdapter()
    {
        $params = [
            'type' => 'visible',
            'secret_key' => '0xE1a02fB374Bf2',
            'site_key' => '07737dfc-abfa-66ac44365d0c'
        ];

        config()->set('captcha.current', 'hcaptcha');
        config()->set('captcha.hcaptcha', $params);

        $captcha = CaptchaFactory::get();

        $this->assertInstanceOf(HcaptchaAdapter::class, $captcha->getAdapter());
    }

    public function testCacheFactoryInvalidTypeAdapter()
    {
        config()->set('captcha.current', 'invalid');

        $this->expectException(CaptchaException::class);

        $this->expectExceptionMessage('The adapter `invalid` is not supported`');

        CaptchaFactory::get();
    }

    public function testAuthFactoryReturnsSameInstance()
    {
        $captcha1 = CaptchaFactory::get();
        $captcha2 = CaptchaFactory::get();

        $this->assertSame($captcha1, $captcha2);
    }
}