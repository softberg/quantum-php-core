<?php

namespace Quantum\Libraries\JWToken {

    function env($key)
    {
        return 'somerandomstring';
    }

}

namespace Quantum\Test\Unit {

    use Quantum\Libraries\Storage\FileSystem;
    use Quantum\Libraries\JWToken\JWToken;
    use Quantum\Exceptions\AuthException;
    use Quantum\Libraries\Hasher\Hasher;
    use Quantum\Libraries\Auth\ApiAuth;
    use Quantum\Libraries\Config\Config;
    use Quantum\Libraries\Auth\User;
    use PHPUnit\Framework\TestCase;
    use Quantum\Loader\Loader;
    use Mockery;


    function auto_increment(array $collection, string $field)
    {
        $max = 0;
        foreach ($collection as $item) {
            $max = max($max, $item[$field]);
        }
        return ++$max;
    }


    class ApiAuthTest extends TestCase
    {

        private $apiAuth;
        private $authService;
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
            'otp' => '',
            'otp_expiry_in' => '',
            'otp_token' => ''
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

            $loader = new Loader(new FileSystem);

            $loader->loadDir(dirname(__DIR__, 4) . DS . 'src' . DS . 'Helpers' . DS . 'functions');

            $loader->loadFile(dirname(__DIR__, 4) . DS . 'src' . DS . 'constants.php');

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

            $mailer = Mockery::mock('Quantum\Libraries\Mailer\Mailer');

            $mailer->shouldReceive('setFrom')->andReturn($mailer);

            $mailer->shouldReceive('setAddress')->andReturn($mailer);

            $mailer->shouldReceive('setSubject')->andReturn($mailer);

            $mailer->shouldReceive('setBody')->andReturn($mailer);

            $mailer->shouldReceive('setTemplate')->andReturn($mailer);

            $mailer->shouldReceive('send')->andReturn(true);

            $jwt = (new JWToken())
                ->setLeeway(1)
                ->setClaims([
                    'jti' => uniqid(),
                    'iss' => 'issuer',
                    'aud' => 'audience',
                    'iat' => time(),
                    'nbf' => time() + 1,
                    'exp' => time() + 60
                ]);

            config()->set('langs', true);

            $this->apiAuth = ApiAuth::getInstance($this->authService, $mailer, new Hasher, $jwt);

            $admin = $this->apiAuth->signup($this->adminUser);

            $this->apiAuth->activate($admin->getFieldValue('activation_token'));
        }

        public function tearDown(): void
        {
            self::$users = [];

            $this->apiAuth->signout();
        }

        public function testApiAuthConstructor()
        {
            $this->assertInstanceOf('Quantum\Libraries\Auth\ApiAuth', $this->apiAuth);
        }

        public function testApiSigninIncorrectCredetials()
        {
            $this->expectException(AuthException::class);

            $this->expectExceptionMessage(AuthException::INCORRECT_AUTH_CREDENTIALS);

            $this->apiAuth->signin('admin@qt.com', '111111');
        }

        public function testApiSigninCorrectCredentials()
        {
            $configData = [
                '2SV' => false
            ];

            $loader = Mockery::mock('Quantum\Loader\Loader');

            $loader->shouldReceive('setup')->andReturn($loader);

            $loader->shouldReceive('load')->andReturn($configData);

            Config::getInstance()->load($loader);

            $this->assertIsArray($this->apiAuth->signin('admin@qt.com', 'qwerty'));

            $this->assertArrayHasKey('access_token', $this->apiAuth->signin('admin@qt.com', 'qwerty'));

            $this->assertArrayHasKey('refresh_token', $this->apiAuth->signin('admin@qt.com', 'qwerty'));
        }

        public function testApiSignOut()
        {
            $tokens = $this->apiAuth->signin('admin@qt.com', 'qwerty');

            $this->assertTrue($this->apiAuth->check());

            $this->apiAuth->signout();

            $this->assertFalse($this->apiAuth->check());
        }

        public function testApiUser()
        {
            $tokens = $this->apiAuth->signin('admin@qt.com', 'qwerty');

            $this->assertEquals('admin@qt.com', $this->apiAuth->user()->getFieldValue('email'));

            $this->assertEquals('admin', $this->apiAuth->user()->getFieldValue('role'));

            $this->apiAuth->signout();

            $this->assertNull($this->apiAuth->user());
        }

        public function testApiCheck()
        {
            $this->assertFalse($this->apiAuth->check());

            $this->apiAuth->signin('admin@qt.com', 'qwerty');

            $this->assertTrue($this->apiAuth->check());

            $this->apiAuth->signout();

            $this->assertFalse($this->apiAuth->check());
        }

        public function testApiSignupAndSigninWithoutActivation()
        {

            $this->expectException(AuthException::class);

            $this->expectExceptionMessage(AuthException::INACTIVE_ACCOUNT);

            $this->apiAuth->signup($this->guestUser);

            $this->assertTrue($this->apiAuth->signin('guest@qt.com', '123456'));
        }

        public function testApiSignupAndActivteAccount()
        {
            $user = $this->apiAuth->signup($this->guestUser);

            $this->apiAuth->activate($user->getFieldValue('activation_token'));

            $this->assertIsArray($this->apiAuth->signin('guest@qt.com', '123456'));

            $this->assertArrayHasKey('access_token', $this->apiAuth->signin('guest@qt.com', '123456'));

            $this->assertArrayHasKey('refresh_token', $this->apiAuth->signin('guest@qt.com', '123456'));
        }

        public function testApiForgetReset()
        {
            $resetToken = $this->apiAuth->forget('admin@qt.com', 'tpl');

            $this->apiAuth->reset($resetToken, '123456789');

            $this->assertIsArray($this->apiAuth->signin('admin@qt.com', '123456789'));

            $this->assertArrayHasKey('access_token', $this->apiAuth->signin('admin@qt.com', '123456789'));

            $this->assertArrayHasKey('refresh_token', $this->apiAuth->signin('admin@qt.com', '123456789'));
        }

        public function testApiVerify()
        {
            config()->set('2SV', true);

            config()->set('otp_expires', 2);

            $otp_token = $this->apiAuth->signin('admin@qt.com', 'qwerty');

            $tokens = $this->apiAuth->verifyOtp(111111, $otp_token);

            $this->assertArrayHasKey('access_token', $tokens);

            $this->assertArrayHasKey('refresh_token', $tokens);
        }

        public function testApiSigninWithoutVerification()
        {
            config()->set('2SV', false);

            config()->set('otp_expires', 2);

            $this->assertArrayHasKey('access_token', $this->apiAuth->signin('admin@qt.com', 'qwerty'));

            $this->assertArrayHasKey('refresh_token', $this->apiAuth->signin('admin@qt.com', 'qwerty'));
        }

        public function testApiSigninWithVerification()
        {
            config()->set('2SV', true);

            config()->set('otp_expires', 2);

            $this->assertIsString($this->apiAuth->signin('admin@qt.com', 'qwerty'));
        }

        public function testApiResendOtp()
        {
            config()->set('2SV', true);

            config()->set('otp_expires', 2);

            $otp_token = $this->apiAuth->signin('admin@qt.com', 'qwerty');

            $this->assertIsString($this->apiAuth->resendOtp($otp_token));
        }

    }

}