<?php

namespace Quantum\Tests\Libraries\Captcha;

use Quantum\Libraries\Captcha\Exceptions\CaptchaException;
use Quantum\Libraries\Captcha\Contracts\CaptchaInterface;
use Quantum\Libraries\Captcha\Adapters\RecaptchaAdapter;
use Quantum\Libraries\HttpClient\HttpClient;
use Quantum\Libraries\Captcha\Captcha;
use Quantum\Tests\AppTestCase;

class CaptchaTest extends AppTestCase
{

    public function setUp(): void
    {
        parent::setUp();
    }

    public function testCaptchaGetAdapter()
    {
        $params = [
            'type' => 'visible',
            'secret_key' => '0xE1a02fB374Bf2',
            'site_key' => '07737dfc-abfa-66ac44365d0c'
        ];

        $captcha = new Captcha(new RecaptchaAdapter($params, new HttpClient()));

        $this->assertInstanceOf(RecaptchaAdapter::class, $captcha->getAdapter());

        $this->assertInstanceOf(CaptchaInterface::class, $captcha->getAdapter());
    }

    public function testCaptchaCallingValidMethod()
    {
        $params = [
            'type' => 'visible',
            'secret_key' => '0xE1a02fB374Bf2',
            'site_key' => '07737dfc-abfa-66ac44365d0c'
        ];

        $captcha = new Captcha(new RecaptchaAdapter($params, new HttpClient()));

        $this->assertEquals('visible', $captcha->getType());
    }

    public function testCacheCallingInvalidMethod()
    {
        $params = [
            'type' => 'visible',
            'secret_key' => '0xE1a02fB374Bf2',
            'site_key' => '07737dfc-abfa-66ac44365d0c'
        ];

        $captcha = new Captcha(new RecaptchaAdapter($params, new HttpClient()));

        $this->expectException(CaptchaException::class);

        $this->expectExceptionMessage('The method `callingInvalidMethod` is not supported for `' . RecaptchaAdapter::class . '`');

        $captcha->callingInvalidMethod();
    }
}