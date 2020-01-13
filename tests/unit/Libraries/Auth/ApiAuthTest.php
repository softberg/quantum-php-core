<?php

namespace Quantum\Libraries\JWToken {

    function env($key)
    {
        return 'somerandomstring';
    }

}

namespace Quantum\Test\Unit {

    use Mockery;
    use PHPUnit\Framework\TestCase;
    use Quantum\Http\Request;
    use Quantum\Libraries\Auth\ApiAuth;
    use Quantum\Libraries\Hasher\Hasher;
    use Quantum\Libraries\JWToken\JWToken;
    use Quantum\Exceptions\AuthException;
    use Quantum\Exceptions\ExceptionMessages;

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
        ];
        protected $visibleFields = [
            'username',
            'firstname',
            'lastname',
            'role'
        ];

        public function setUp(): void
        {
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

            $this->mailer->shouldReceive('createFrom')->andReturn($this->mailer);

            $this->mailer->shouldReceive('createAddresses')->andReturn($this->mailer);

            $this->mailer->shouldReceive('createBody')->andReturn($this->mailer);

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

        public function testSigninIncorrectCredetials()
        {
            $this->expectException(AuthException::class);

            $this->expectExceptionMessage(ExceptionMessages::INCORRECT_AUTH_CREDENTIALS);

            $this->apiAuth->signin('admin@qt.com', '111111');
        }

        public function testSigninCorrectCredentials()
        {
            $this->assertIsArray($this->apiAuth->signin('admin@qt.com', 'qwerty'));

            $this->assertArrayHasKey('access_token', $this->apiAuth->signin('admin@qt.com', 'qwerty'));

            $this->assertArrayHasKey('refresh_token', $this->apiAuth->signin('admin@qt.com', 'qwerty'));
        }

        public function testSignOut()
        {
            $tokens = $this->apiAuth->signin('admin@qt.com', 'qwerty');

            Request::setHeader('AUTHORIZATION', 'Bearer ' . $tokens['access_token']);

            Request::setHeader('refresh_token', $tokens['refresh_token']);

            $this->assertTrue($this->apiAuth->check());

            $this->apiAuth->signout();

            $this->assertFalse($this->apiAuth->check());
        }

        public function testUser()
        {
            $tokens = $this->apiAuth->signin('admin@qt.com', 'qwerty');

            Request::setHeader('AUTHORIZATION', 'Bearer ' . $tokens['access_token']);

            Request::setHeader('refresh_token', $tokens['refresh_token']);

            $this->assertEquals('admin@qt.com', $this->apiAuth->user()->username);

            $this->assertEquals('admin', $this->apiAuth->user()->role);

            Request::deleteHeader('AUTHORIZATION');

            $this->assertEquals('admin@qt.com', $this->apiAuth->user()->username);

            $this->apiAuth->signout();
        }

        public function testCheck()
        {
            $this->assertFalse($this->apiAuth->check());

            $tokens = $this->apiAuth->signin('admin@qt.com', 'qwerty');

            Request::setHeader('AUTHORIZATION', 'Bearer ' . $tokens['access_token']);

            Request::setHeader('refresh_token', $tokens['refresh_token']);

            $this->assertTrue($this->apiAuth->check());
        }

        public function testSignupAndSigninWithoutActivation()
        {

            $this->expectException(AuthException::class);

            $this->expectExceptionMessage(ExceptionMessages::INACTIVE_ACCOUNT);

            $this->apiAuth->signup($this->mailer, $this->guestUser);

            $this->assertTrue($this->apiAuth->signin('guest@qt.com', '123456'));
        }

        public function testSignupAndActivteAccount()
        {
            $user = $this->apiAuth->signup($this->mailer, $this->guestUser);

            $this->apiAuth->activate($user['activation_token']);

            $this->assertIsArray($this->apiAuth->signin('guest@qt.com', '123456'));

            $this->assertArrayHasKey('access_token', $this->apiAuth->signin('guest@qt.com', '123456'));

            $this->assertArrayHasKey('refresh_token', $this->apiAuth->signin('guest@qt.com', '123456'));
        }

        public function testForgetReset()
        {
            $resetToken = $this->apiAuth->forget($this->mailer, 'admin@qt.com', 'tpl');

            $this->apiAuth->reset($resetToken, '123456789');

            $this->assertIsArray($this->apiAuth->signin('admin@qt.com', '123456789'));

            $this->assertArrayHasKey('access_token', $this->apiAuth->signin('admin@qt.com', '123456789'));

            $this->assertArrayHasKey('refresh_token', $this->apiAuth->signin('admin@qt.com', '123456789'));
        }

    }

}