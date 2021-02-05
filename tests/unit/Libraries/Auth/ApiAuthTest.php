<?php

namespace Quantum\Libraries\JWToken {

    function env($key)
    {
        return 'somerandomstring';
    }

}

namespace Quantum\Test\Unit {

    use Quantum\Exceptions\ExceptionMessages;
    use Quantum\Libraries\JWToken\JWToken;
    use Quantum\Exceptions\AuthException;
    use Quantum\Libraries\Hasher\Hasher;
    use Quantum\Libraries\Auth\ApiAuth;
    use PHPUnit\Framework\TestCase;
    use Quantum\Loader\Loader;
    use Mockery;
    use Quantum\Libraries\Config\Config;

    class ApiAuthTest extends TestCase
    {

        private $apiAuth;
        private $authService;
        private $mailer;
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

            $this->apiAuth = new ApiAuth($this->authService, new Hasher, $jwt);

            $admin = $this->apiAuth->signup($this->mailer, $this->adminUser);

            $this->apiAuth->activate($admin['activation_token']);
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

            $this->expectExceptionMessage(ExceptionMessages::INCORRECT_AUTH_CREDENTIALS);

            $this->apiAuth->signin($this->mailer, 'admin@qt.com', '111111');
        }

        public function testApiSigninCorrectCredentials()
        {

            $configData = [
                'tow_step_verification' => true
            ];

            $loader = Mockery::mock('Quantum\Loader\Loader');

            $loader->shouldReceive('setup')->andReturn($loader);

            $loader->shouldReceive('load')->andReturn($configData);

            Config::getInstance()->load($loader);

            $this->assertIsArray($this->apiAuth->signin($this->mailer, 'admin@qt.com', 'qwerty'));

            $this->assertArrayHasKey('access_token', $this->apiAuth->signin($this->mailer, 'admin@qt.com', 'qwerty'));

            $this->assertArrayHasKey('refresh_token', $this->apiAuth->signin($this->mailer, 'admin@qt.com', 'qwerty'));

            $this->assertIsInt($this->apiAuth->user()->verification_code);
        }

        public function testApiSignOut()
        {
            $tokens = $this->apiAuth->signin($this->mailer, 'admin@qt.com', 'qwerty');

            $this->assertTrue($this->apiAuth->check());

            $this->apiAuth->signout();

            $this->assertFalse($this->apiAuth->check());
        }

        public function testApiUser()
        {
            $tokens = $this->apiAuth->signin($this->mailer, 'admin@qt.com', 'qwerty');

            $this->assertEquals('admin@qt.com', $this->apiAuth->user()->username);

            $this->assertEquals('admin', $this->apiAuth->user()->role);

            $this->apiAuth->signout();

            $this->assertNull($this->apiAuth->user());
        }

        public function testApiCheck()
        {
            $this->assertFalse($this->apiAuth->check());

            $this->apiAuth->signin($this->mailer, 'admin@qt.com', 'qwerty');

            $this->assertTrue($this->apiAuth->check());
            
            $this->apiAuth->signout();
            
            $this->assertFalse($this->apiAuth->check());
        }

        public function testApiSignupAndSigninWithoutActivation()
        {

            $this->expectException(AuthException::class);

            $this->expectExceptionMessage(ExceptionMessages::INACTIVE_ACCOUNT);

            $this->apiAuth->signup($this->mailer, $this->guestUser);

            $this->assertTrue($this->apiAuth->signin($this->mailer, 'guest@qt.com', '123456'));
        }

        public function testApiSignupAndActivteAccount()
        {
            $user = $this->apiAuth->signup($this->mailer, $this->guestUser);

            $this->apiAuth->activate($user['activation_token']);

            $this->assertIsArray($this->apiAuth->signin($this->mailer, 'guest@qt.com', '123456'));

            $this->assertArrayHasKey('access_token', $this->apiAuth->signin($this->mailer, 'guest@qt.com', '123456'));

            $this->assertArrayHasKey('refresh_token', $this->apiAuth->signin($this->mailer, 'guest@qt.com', '123456'));
        }

        public function testApiForgetReset()
        {
            $resetToken = $this->apiAuth->forget($this->mailer, 'admin@qt.com', 'tpl');

            $this->apiAuth->reset($resetToken, '123456789');

            $this->assertIsArray($this->apiAuth->signin($this->mailer, 'admin@qt.com', '123456789'));

            $this->assertArrayHasKey('access_token', $this->apiAuth->signin($this->mailer, 'admin@qt.com', '123456789'));

            $this->assertArrayHasKey('refresh_token', $this->apiAuth->signin($this->mailer, 'admin@qt.com', '123456789'));
        }

        public function testApiVerify()
        {
            $configData = [
                'tow_step_verification' => true
            ];

            $loader = Mockery::mock('Quantum\Loader\Loader');

            $loader->shouldReceive('setup')->andReturn($loader);

            $loader->shouldReceive('load')->andReturn($configData);

            Config::getInstance()->load($loader);

            $this->apiAuth->signin($this->mailer, 'admin@qt.com', 'qwerty');

            $tokens = $this->apiAuth->verify();

            $this->assertArrayHasKey('access_token',$tokens);

            $this->assertArrayHasKey('refresh_token',$tokens);

            $this->assertFalse($this->apiAuth->checkVerification());
        }

        public function testApiWithoutVerification()
        {
            $configData = [
                'tow_step_verification' => false
            ];

            $loader = Mockery::mock('Quantum\Loader\Loader');

            $loader->shouldReceive('setup')->andReturn($loader);

            $loader->shouldReceive('load')->andReturn($configData);

            Config::getInstance()->load($loader);

            $this->apiAuth->signin($this->mailer, 'admin@qt.com', 'qwerty');

            $this->assertFalse($this->apiAuth->checkVerification());
        }

        public function testApiWithVerification()
        {
            $configData = [
                'tow_step_verification' => true
            ];

            $loader = Mockery::mock('Quantum\Loader\Loader');

            $loader->shouldReceive('setup')->andReturn($loader);

            $loader->shouldReceive('load')->andReturn($configData);

            Config::getInstance()->load($loader);

            $this->apiAuth->signin($this->mailer, 'admin@qt.com', 'qwerty');

            $this->assertTrue($this->apiAuth->checkVerification());
        }
    }
}