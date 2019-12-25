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
}

namespace Quantum\Test\Unit {

    use Mockery;
    use PHPUnit\Framework\TestCase;
    use Quantum\Libraries\Auth\WebAuth;
    use Quantum\Libraries\Hasher\Hasher;

    class WebAuthTest extends TestCase
    {
        private $authService;

        private $mailer;

        private $webAuth;

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
            'usernameKey' => 'username',
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

            $this->authService->shouldReceive('get')->andReturnUsing(function ($field, $value) {
                if ($value) {
                    foreach (self::$users as $user) {
                        if (in_array($value, $user)) {
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

            $this->webAuth = new WebAuth($this->authService, new Hasher);

            $this->webAuth->signout();
        }

        public function testWebAuthConstructor()
        {
            $this->assertInstanceOf('Quantum\Libraries\Auth\WebAuth', $this->webAuth);
        }

        public function testSignin()
        {
            $this->assertFalse($this->webAuth->signin('guest@qt.com', '123456'));

            $this->assertTrue($this->webAuth->signin('admin@qt.com', 'qwerty'));

            $this->assertTrue($this->webAuth->signin('admin@qt.com', 'qwerty', true));
        }

        public function testSignout()
        {
            $this->assertFalse(\Quantum\Libraries\Auth\session()->has('auth_user'));

            $this->webAuth->signin('admin@qt.com', 'qwerty');

            $this->assertTrue(\Quantum\Libraries\Auth\session()->has('auth_user'));

            $this->webAuth->signout();

            $this->assertFalse(\Quantum\Libraries\Auth\session()->has('auth_user'));

        }

        public function testUser()
        {
            $this->webAuth->signin('admin@qt.com', 'qwerty');

            $this->assertEquals('admin@qt.com', $this->webAuth->user()->username);

            $this->assertEquals('admin', $this->webAuth->user()->role);

            $this->webAuth->signin('admin@qt.com', 'qwerty', true);

            \Quantum\Libraries\Auth\session()->delete('auth_user');

            $this->assertEquals('admin@qt.com', $this->webAuth->user()->username);
        }

        public function testCheck()
        {
            $this->assertFalse($this->webAuth->check());

            $this->webAuth->signin('admin@qt.com', 'qwerty');

            $this->assertTrue($this->webAuth->check());
        }

        public function testSignup()
        {
            $this->assertFalse($this->webAuth->signin('guest@qt.com', '123456'));

            $this->webAuth->signup($this->newUser);

            $this->assertTrue($this->webAuth->signin('guest@qt.com', '123456'));
        }

        public function testForgetReset()
        {
            $resetToken = $this->webAuth->forget($this->mailer, 'admin@qt.com', 'tpl');

            $this->webAuth->reset($resetToken, '123456789');

            $this->assertFalse($this->webAuth->signin('admin@qt.com', 'qwerty'));

            $this->assertTrue($this->webAuth->signin('admin@qt.com', '123456789'));
        }
    }
}