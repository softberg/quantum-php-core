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

    class ApiAuthTest extends TestCase
    {
        private $apiAuth;

        private $authService;

        private $mailer;

        private static $users = [
            [
                'username' => 'admin@qt.com',
                'firstname' => 'Admin',
                'lastname' => 'User',
                'role' => 'admin',
                'password' => '$2y$12$0M78WcmUZYQq85vHZLoNW.CyDUezRxh9Ye8/Z8oWCwJmBrz8p.j7C',
                'remember_token' => '',
                'reset_token' => '',
                'access_token' => '',
                'refresh_token' => '',
            ]
        ];

        private $newUser = [
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
            'passwordKey' => 'password',
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

            $this->authService->shouldReceive('get')->andReturnUsing(function ($field) {
                if ($field) {
                    foreach (self::$users as $user) {
                        if (in_array($field, $user)) {
                            return (object)$user;
                        }
                    }
                }
                return null;
            });

            $this->authService->shouldReceive('update')->andReturnUsing(function ($field, $data) {
                $allFields = array_merge($this->fields, array_values($this->keyFields));
                if ($field) {
                    foreach (self::$users as &$user) {
                        if (in_array($field, $user)) {
                            foreach ($data as $key => $value) {
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

                return (object)$user;
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

            $this->apiAuth->signout();
        }

        public function testApiAuthConstructor()
        {
            $this->assertInstanceOf('Quantum\Libraries\Auth\ApiAuth', $this->apiAuth);
        }

        public function testSignin()
        {
            $this->assertFalse($this->apiAuth->signin('guest@qt.com', '123456'));

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
        }

        public function testCheck()
        {
            $this->assertFalse($this->apiAuth->check());

            $tokens = $this->apiAuth->signin('admin@qt.com', 'qwerty');

            Request::setHeader('AUTHORIZATION', 'Bearer ' . $tokens['access_token']);

            Request::setHeader('refresh_token', $tokens['refresh_token']);

            $this->assertTrue($this->apiAuth->check());
        }

        public function testSignup()
        {
            $this->assertFalse($this->apiAuth->signin('guest@qt.com', '123456'));

            $this->apiAuth->signup($this->newUser);

            $this->assertIsArray($this->apiAuth->signin('guest@qt.com', '123456'));

            $this->assertArrayHasKey('access_token', $this->apiAuth->signin('guest@qt.com', '123456'));

            $this->assertArrayHasKey('refresh_token', $this->apiAuth->signin('guest@qt.com', '123456'));
        }

        public function testForgetReset()
        {
            $resetToken = $this->apiAuth->forget($this->mailer, 'admin@qt.com', 'tpl');

            $this->apiAuth->reset($resetToken, '123456789');

            $this->assertFalse($this->apiAuth->signin('admin@qt.com', 'qwerty'));

            $this->assertIsArray($this->apiAuth->signin('admin@qt.com', '123456789'));

            $this->assertArrayHasKey('access_token', $this->apiAuth->signin('admin@qt.com', '123456789'));

            $this->assertArrayHasKey('refresh_token', $this->apiAuth->signin('admin@qt.com', '123456789'));
        }
    }
}