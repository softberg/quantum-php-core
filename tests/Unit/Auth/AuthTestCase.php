<?php

namespace Quantum\Auth\Traits {

    function random_number(int $length = 10): int
    {
        return 123456789;
    }
}

namespace Quantum\Tests\Unit\Auth {

    use Quantum\Auth\Contracts\AuthServiceInterface;
    use Quantum\Database\Adapters\Sleekdb\SleekDbal;
    use Quantum\Tests\Unit\AppTestCase;
    use Quantum\Mailer\Mailer;
    use Quantum\Loader\Setup;
    use Quantum\Auth\User;
    use Mockery;

    function auto_increment(array $collection, string $field)
    {
        $max = 0;
        foreach ($collection as $item) {
            $max = max($max, $item[$field]);
        }
        return ++$max;
    }

    abstract class AuthTestCase extends AppTestCase
    {
        protected static $users = [];
        protected $authService;
        protected $mailer;
        protected $userSchema = [
            'id' => ['name' => 'id', 'visible' => false],
            'firstname' => ['name' => 'firstname', 'visible' => true],
            'lastname' => ['name' => 'lastname', 'visible' => true],
            'role' => ['name' => 'role', 'visible' => true],
            'username' => ['name' => 'email', 'visible' => true],
            'password' => ['name' => 'password', 'visible' => false],
            'uuid' => ['name' => 'uuid', 'visible' => true],
            'activationToken' => ['name' => 'activation_token', 'visible' => false],
            'rememberToken' => ['name' => 'remember_token', 'visible' => false],
            'resetToken' => ['name' => 'reset_token', 'visible' => false],
            'accessToken' => ['name' => 'access_token', 'visible' => false],
            'refreshToken' => ['name' => 'refresh_token', 'visible' => false],
            'otp' => ['name' => 'otp', 'visible' => false],
            'otpExpiry' => ['name' => 'otp_expires', 'visible' => false],
            'otpToken' => ['name' => 'otp_token', 'visible' => false],
        ];
        protected $adminUser = [
            'email' => 'admin@qt.com',
            'firstname' => 'Admin',
            'lastname' => 'User',
            'role' => 'admin',
            'password' => 'qwerty',
            'uuid' => 'admin-uuid',
            'activation_token' => '',
            'remember_token' => '',
            'reset_token' => '',
            'access_token' => '',
            'refresh_token' => '',
            'otp' => '',
            'otp_expiry_in' => '',
            'otp_token' => '',
        ];
        protected $guestUser = [
            'email' => 'guest@qt.com',
            'password' => '123456',
            'firstname' => 'Guest',
            'lastname' => 'User',
        ];

        public function setUp(): void
        {
            parent::setUp();

            if (!config()->has('database')) {
                config()->import(new Setup('config', 'database'));
            }

            config()->set('database.default', 'sleekdb');

            SleekDbal::connect(config()->get('database.sleekdb'));

            $this->authService = Mockery::mock(AuthServiceInterface::class);

            $this->authService->shouldReceive('userSchema')->andReturn($this->userSchema);

            $this->authService->shouldReceive('get')->andReturnUsing(function ($field, $value): ?\Quantum\Auth\User {
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

            $this->authService->shouldReceive('add')->andReturnUsing(function ($data): \Quantum\Auth\User {

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

            $this->mailer = Mockery::mock(Mailer::class);

            $this->mailer->shouldReceive('setFrom')->andReturn($this->mailer);

            $this->mailer->shouldReceive('setAddress')->andReturn($this->mailer);

            $this->mailer->shouldReceive('setSubject')->andReturn($this->mailer);

            $this->mailer->shouldReceive('setBody')->andReturn($this->mailer);

            $this->mailer->shouldReceive('setTemplate')->andReturn($this->mailer);

            $this->mailer->shouldReceive('send')->andReturn(true);
        }
    }
}
