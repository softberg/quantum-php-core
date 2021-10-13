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

//    function get_config($key)
//    {
//        return $key;
//    }

    function random_number()
    {
        return 111111;
    }

}

namespace Quantum\Test\Unit {

    use Quantum\App;
    use Quantum\Di\Di;
    use Quantum\Libraries\Storage\FileSystem;
    use Quantum\Exceptions\AuthException;
    use Quantum\Libraries\Hasher\Hasher;
    use Quantum\Libraries\Auth\WebAuth;
    use Quantum\Libraries\Auth\User;
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
            'email' => 'admin@qt.com',
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
            'email' => 'guest@qt.com',
            'password' => '123456',
            'firstname' => 'Guest',
            'lastname' => 'User',
        ];

        protected $userSchema = [
            'id' => ['name' => 'id', 'visible' => false],
            'firstname' => ['name' => 'firstname', 'visible' => true],
            'lastname' => ['name' => 'lastname', 'visible' => true],
            'role' => ['name' => 'role', 'visible' => true],
            'username' => ['name' => 'email', 'visible' => true],
            'password' => ['name' => 'password', 'visible' => false],
            'activationToken' => ['name' => 'activation_token', 'visible' => false],
            'rememberToken' => ['name' => 'remember_token', 'visible' => false],
            'resetToken' => ['name' => 'reset_token', 'visible' => false],
            'accessToken' => ['name' => 'access_token', 'visible' => false],
            'refreshToken' => ['name' => 'refresh_token', 'visible' => false],
            'otp' => ['name' => 'otp', 'visible' => false],
            'otpExpiry' => ['name' => 'otp_expires', 'visible' => false],
            'otpToken' => ['name' => 'otp_token', 'visible' => false],
        ];

        public function setUp(): void
        {
            App::loadCoreFunctions(dirname(__DIR__, 4) . DS . 'src' . DS . 'Helpers');

            App::setBaseDir(dirname(__DIR__, 2) . DS . '_root');

            Di::loadDefinitions();

            config()->flush();

            $this->authService = Mockery::mock('Quantum\Libraries\Auth\AuthServiceInterface');

            $this->authService->shouldReceive('userSchema')->andReturn($this->userSchema);

            $this->authService->shouldReceive('get')->andReturnUsing(function ($field, $value) {
                foreach (self::$users as $userData) {
                    if (in_array($value, $userData)) {
                        return (new User())->setData($userData);
                    }
                }

                return null;
            });

            $this->authService->shouldReceive('update')->andReturnUsing(function ($field, $value, $data) {
                $user = $this->authService->get($field, $value);

                if (!$user) {
                    return null;
                }

                foreach ($data as $key => $val) {
                    if ($user->hasField($key)) {
                        $user->setFieldValue($key, $val ?? '');
                    }
                }

                foreach (self::$users as &$userData) {
                    if (in_array($user->getFieldValue('id'), $userData)) {
                        $userData = $user->getData();
                    }
                }

                return $user;
            });

            $this->authService->shouldReceive('add')->andReturnUsing(function ($data) {
                $user = new User();

                $user->setFields($this->authService->userSchema());

                foreach ($data as $key => $val) {
                    foreach ($this->authService->userSchema() as $field) {
                        if (isset($field['name'])) {
                            if ($field['name'] == 'id') {
                                $user->setFieldValue('id', auto_increment(self::$users, 'id'));
                            }

                            if ($field['name'] == $key) {
                                $user->setFieldValue($key, $val ?? '');
                            }
                        }
                    }
                }

                self::$users[] = $user->getData();

                return $user;

            });

            $this->mailer = Mockery::mock('Quantum\Libraries\Mailer\Mailer');

            $this->mailer->shouldReceive('setFrom')->andReturn($this->mailer);

            $this->mailer->shouldReceive('setAddress')->andReturn($this->mailer);

            $this->mailer->shouldReceive('setSubject')->andReturn($this->mailer);

            $this->mailer->shouldReceive('setBody')->andReturn($this->mailer);

            $this->mailer->shouldReceive('setTemplate')->andReturn($this->mailer);

            $this->mailer->shouldReceive('send')->andReturn(true);

            config()->set('langs', true);

            $this->webAuth = WebAuth::getInstance($this->authService, $this->mailer, new Hasher);

            $admin = $this->webAuth->signup($this->adminUser);

            $this->webAuth->activate($admin->getFieldValue('activation_token'));
        }

        public function tearDown(): void
        {
            self::$users = [];

            $this->webAuth->signout();
        }

        public function testWebAuthConstructor()
        {
            $this->assertInstanceOf(WebAuth::class, $this->webAuth);
        }

        public function testWebSigninIncorrectCredetials()
        {
            $this->expectException(AuthException::class);

            $this->expectExceptionMessage(AuthException::INCORRECT_AUTH_CREDENTIALS);

            $this->webAuth->signin('admin@qt.com', '111111');
        }

        public function testWebSigninCorrectCredentials()
        {
            $this->assertTrue($this->webAuth->signin('admin@qt.com', 'qwerty'));

            $this->assertTrue($this->webAuth->signin('admin@qt.com', 'qwerty', true));
        }

        public function testWebSignout()
        {
            $this->assertFalse(\Quantum\Libraries\Auth\session()->has('auth_user'));

            $this->webAuth->signin('admin@qt.com', 'qwerty');

            $this->assertTrue(\Quantum\Libraries\Auth\session()->has('auth_user'));

            $this->webAuth->signout();

            $this->assertFalse(\Quantum\Libraries\Auth\session()->has('auth_user'));
        }

        public function testWebUser()
        {
            $this->webAuth->signin('admin@qt.com', 'qwerty');

            $this->assertEquals('admin@qt.com', $this->webAuth->user()->getFieldValue('email'));

            $this->assertEquals('admin', $this->webAuth->user()->getFieldValue('role'));

            $this->webAuth->signin('admin@qt.com', 'qwerty', true);

            \Quantum\Libraries\Auth\session()->delete('auth_user');

            $this->assertEquals('admin@qt.com', $this->webAuth->user()->getFieldValue('email'));
        }

        public function testWebCheck()
        {
            $this->assertFalse($this->webAuth->check());

            $this->webAuth->signin('admin@qt.com', 'qwerty');

            $this->assertTrue($this->webAuth->check());
        }

        public function testWebSignupAndSigninWithoutActivation()
        {

            $this->expectException(AuthException::class);

            $this->expectExceptionMessage(AuthException::INACTIVE_ACCOUNT);

            $this->webAuth->signup($this->guestUser);

            $this->assertTrue($this->webAuth->signin('guest@qt.com', '123456'));
        }

        public function testWebSignupAndActivteAccount()
        {
            $user = $this->webAuth->signup($this->guestUser);

            $this->webAuth->activate($user->getFieldValue('activation_token'));

            $this->assertTrue($this->webAuth->signin('guest@qt.com', '123456'));
        }

        public function testWebForgetReset()
        {
            $resetToken = $this->webAuth->forget('admin@qt.com', 'tpl');

            $this->webAuth->reset($resetToken, '123456789');

            $this->assertTrue($this->webAuth->signin('admin@qt.com', '123456789'));
        }

        public function testWebWithoutVerification()
        {
            config()->set('2SV', false);

            config()->set('otp_expiry_time', 2);

            $this->assertTrue($this->webAuth->signin('admin@qt.com', 'qwerty'));
        }

        public function testWebWithVerification()
        {
            config()->set('2SV', true);

            config()->set('otp_expires', 2);

            $this->assertIsString($this->webAuth->signin('admin@qt.com', 'qwerty'));
        }

        public function testWebVerify()
        {
            config()->set('2SV', true);

            config()->set('otp_expires', 2);

            $otp_token = $this->webAuth->signin('admin@qt.com', 'qwerty');

            $this->assertTrue($this->webAuth->verifyOtp(111111, $otp_token));
        }

        public function testWebResendOtp()
        {
            config()->set('2SV', true);

            config()->set('otp_expires', 2);

            $otp_token = $this->webAuth->signin('admin@qt.com', 'qwerty');

            $this->assertIsString($this->webAuth->resendOtp($otp_token));
        }

    }

}