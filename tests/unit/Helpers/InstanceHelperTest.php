<?php

namespace Quantum\Tests\Helpers;

use Quantum\Libraries\Auth\AuthenticatableInterface;
use Quantum\Libraries\Captcha\CaptchaInterface;
use Quantum\Libraries\Mailer\MailerInterface;
use Quantum\Libraries\Asset\AssetManager;
use Quantum\Libraries\Session\Session;
use Quantum\Libraries\Cookie\Cookie;
use Quantum\Libraries\Cache\Cache;
use Quantum\Libraries\Csrf\Csrf;
use Quantum\Tests\AppTestCase;
use Quantum\Hooks\HookManager;

class InstanceHelperTest extends AppTestCase
{

    public function setUp(): void
    {
        parent::setUp();
    }

    public function testSessionHelper()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $this->assertInstanceOf(Session::class, session());
    }

    public function testCookieHelper()
    {
        $this->assertInstanceOf(Cookie::class, cookie());
    }

    public function testAuthHelper()
    {
        $this->assertInstanceOf(AuthenticatableInterface::class, auth());
    }

    public function testMailerHelper()
    {
        $this->assertInstanceOf(MailerInterface::class, mailer());
    }

    public function testAssetHelper()
    {
        $this->assertInstanceOf(AssetManager::class, asset());
    }

    public function testHookHelper()
    {
        $this->assertInstanceOf(HookManager::class, hook());
    }

    public function testCacheHelper()
    {
        $this->assertInstanceOf(Cache::class, cache());
    }

    public function testCsrfHelper()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $this->assertInstanceOf(Csrf::class, csrf());
    }

    public function testCaptchaHelper()
    {
        $this->assertInstanceOf(CaptchaInterface::class, captcha());
    }
}