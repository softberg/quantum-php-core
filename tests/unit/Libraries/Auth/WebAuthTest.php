<?php

namespace Quantum\Libraries\Auth {

    use Quantum\Libraries\Encryption\Cryptor;
    use Quantum\Libraries\Session\Session;
    use Quantum\Libraries\Cookie\Cookie;


$sessionStorage = [];
    $cookieStorage = [];

    function session()
    {
        global $sessionStorage;
        return new Session($sessionStorage, new Cryptor);
    }

    function cookie()
    {
        global $cookieStorage;
        return new Cookie($cookieStorage, new Cryptor);
    }

    function env($key)
    {
        return 'somerandomstring';
    }

    function get_config($key)
    {
        return $key;
    }

    function random_number()
    {
        return 111111;
    }

}

namespace Quantum\Test\Unit {

    use Quantum\Exceptions\ExceptionMessages;
    use Quantum\Exceptions\AuthException;
    use Quantum\Libraries\Hasher\Hasher;
    use Quantum\Libraries\Auth\WebAuth;
    use Quantum\Libraries\Config\Config;
    use PHPUnit\Framework\TestCase;
    use Quantum\Loader\Loader;
    use Mockery;

    class WebAuthTest extends TestCase
    {

        private $authService;
        private $mailer;
        private $webAuth;
        private static $users = [];
        private $adminUser = [
            'username' => 'admin@qt.com',
            'firstname' => 'Admin',
            'lastname' => 'User',
            'role' => 'admin',
            'password' => 'qwerty',
            'activation_token' => '',
            'remember_token' => '',
            'reset_token' => '',
            'access_token' => '',
            'refresh_token' => '',
        ];
        private $guestUser = [
            'username' => 'guest@qt.com',
            'password' => '123456',
            'firstname' => 'Guest',
            'lastname' => 'User',
        ];
        protected $fields = [
            'username',
            'firstname',
            'lastname',
            'role'
        ];
        private $keyFields = [
            'usernameKey' => 'username',
            'passwordKey' => 'password',
            'activationTokenKey' => 'activation_token',
            'rememberTokenKey' => 'remember_token',
            'resetTokenKey' => 'reset_token',
            'accessTokenKey' => 'access_token',
            'refreshTokenKey' => 'refresh_token',
            'verificationCode' => 'verification_code',
        ];
        protected $visibleFields = [
            'username',
            'firstname',
            'lastname',
            'role',
            'verification_code'
        ];

        public function setUp(): void
        {
            $loader = new Loader();

            $loader->loadDir(dirname(__DIR__, 4) . DS . 'src' . DS . 'Helpers' . DS . 'functions');

            config()->flush();

            $this->authService = Mockery::mock('Quantum\Libraries\Auth\AuthServiceInterface');

            $this->authService->shouldReceive('getDefinedKeys')->andReturn($this->keyFields);

            $this->authService->shouldReceive('getVisibleFields')->andReturn($this->visibleFields);

            $this->authService->shouldReceive('get')->andReturnUsing(function ($field, $value) {
                if ($value) {
                    foreach (self::$users as $user) {
                        if (in_array($value, $user)) {
                            return $user;
                        }
                    }
                }
                return [];
            });

            $this->authService->shouldReceive('update')->andReturnUsing(function ($field, $value, $data) {
                $allFields = array_merge($this->fields, array_values($this->keyFields));
                if ($value) {
                    foreach (self::$users as &$user) {
                        if (in_array($value, $user)) {
                            foreach ($data as $key => $val) {
                                if (in_array($key, $allFields)) {
                                    $user[$key] = $data[$key] ?? '';
                                }
                            }
                        }
                    }
                }
            });

            $this->authService->shouldReceive('add')->andReturnUsing(function ($data) {
                $user = [];
                $allFields = array_merge($this->fields, array_values($this->keyFields));
                foreach ($allFields as $field) {
                    $user[$field] = $data[$field] ?? '';
                }

                if (count(self::$users) > 0) {
                    array_push(self::$users, $user);
                } else {
                    self::$users[1] = $user;
                }

                return $user;
            });

            $this->mailer = Mockery::mock('Quantum\Libraries\Mailer\Mailer');

            $this->mailer->shouldReceive('setFrom')->andReturn($this->mailer);

            $this->mailer->shouldReceive('setAddress')->andReturn($this->mailer);

            $this->mailer->shouldReceive('setBody')->andReturn($this->mailer);

            $this->mailer->shouldReceive('send')->andReturn(true);

            $this->webAuth = new WebAuth($this->authService, new Hasher);

            $admin = $this->webAuth->signup($this->mailer, $this->adminUser);

            $this->webAuth->activate($admin['activation_token']);
        }

        public function tearDown(): void
        {
            self::$users = [];

            $this->webAuth->signout();
        }

        public function testWebAuthConstructor()
        {
            $this->assertInstanceOf('Quantum\Libraries\Auth\WebAuth', $this->webAuth);
        }

        public function testWebSigninIncorrectCredetials()
        {
            $this->expectException(AuthException::class);

            $this->expectExceptionMessage(ExceptionMessages::INCORRECT_AUTH_CREDENTIALS);

            $this->webAuth->signin($this->mailer, 'admin@qt.com', '111111');
        }

        public function testWebSigninCorrectCredentials()
        {
            $this->assertTrue($this->webAuth->signin($this->mailer, 'admin@qt.com', 'qwerty'));

            $this->assertTrue($this->webAuth->signin($this->mailer, 'admin@qt.com', 'qwerty', true));
        }

        public function testWebSignout()
        {
            $this->assertFalse(\Quantum\Libraries\Auth\session()->has('auth_user'));

            $this->webAuth->signin($this->mailer, 'admin@qt.com', 'qwerty');

            $this->assertTrue(\Quantum\Libraries\Auth\session()->has('auth_user'));

            $this->webAuth->signout();

            $this->assertFalse(\Quantum\Libraries\Auth\session()->has('auth_user'));
        }

        public function testWebUser()
        {
            $this->webAuth->signin($this->mailer, 'admin@qt.com', 'qwerty');

            $this->assertEquals('admin@qt.com', $this->webAuth->user()->username);

            $this->assertEquals('admin', $this->webAuth->user()->role);

            $this->webAuth->signin($this->mailer,'admin@qt.com', 'qwerty', true);

            \Quantum\Libraries\Auth\session()->delete('auth_user');

            $this->assertEquals('admin@qt.com', $this->webAuth->user()->username);
        }

        public function testWebCheck()
        {
            $this->assertFalse($this->webAuth->check());

            $this->webAuth->signin($this->mailer, 'admin@qt.com', 'qwerty');

            $this->assertTrue($this->webAuth->check());
        }

        public function testWebSignupAndSigninWithoutActivation()
        {

            $this->expectException(AuthException::class);

            $this->expectExceptionMessage(ExceptionMessages::INACTIVE_ACCOUNT);

            $this->webAuth->signup($this->mailer, $this->guestUser);

            $this->assertTrue($this->webAuth->signin($this->mailer, 'guest@qt.com', '123456'));
        }

        public function testWebSignupAndActivteAccount()
        {
            $user = $this->webAuth->signup($this->mailer, $this->guestUser);

            $this->webAuth->activate($user['activation_token']);

            $this->assertTrue($this->webAuth->signin($this->mailer, 'guest@qt.com', '123456'));
        }

        public function testWebForgetReset()
        {
            $resetToken = $this->webAuth->forget($this->mailer, 'admin@qt.com', 'tpl');

            $this->webAuth->reset($resetToken, '123456789');

            $this->assertTrue($this->webAuth->signin($this->mailer, 'admin@qt.com', '123456789'));
        }

        public function testApiWithoutVerification()
        {
            $configData = [
                'two_step_verification' => false
            ];

            $loader = Mockery::mock('Quantum\Loader\Loader');

            $loader->shouldReceive('setup')->andReturn($loader);

            $loader->shouldReceive('load')->andReturn($configData);

            Config::getInstance()->load($loader);

            $this->webAuth->signin($this->mailer, 'admin@qt.com', 'qwerty');

            $this->assertFalse($this->webAuth->checkVerification());
        }

        public function testApiWithVerification()
        {
            $configData = [
                'two_step_verification' => true
            ];

            $loader = Mockery::mock('Quantum\Loader\Loader');

            $loader->shouldReceive('setup')->andReturn($loader);

            $loader->shouldReceive('load')->andReturn($configData);

            Config::getInstance()->load($loader);

            $this->webAuth->signin($this->mailer, 'admin@qt.com', 'qwerty');

            $this->assertTrue($this->webAuth->checkVerification());
        }

        public function testWebVerify()
        {
            $configData = [
                'two_step_verification' => true
            ];

            $loader = Mockery::mock('Quantum\Loader\Loader');

            $loader->shouldReceive('setup')->andReturn($loader);

            $loader->shouldReceive('load')->andReturn($configData);

            Config::getInstance()->load($loader);

            $this->webAuth->signin($this->mailer, 'admin@qt.com', 'qwerty');

            $this->assertTrue($this->webAuth->verify(111111));
        }
    }
}