<?php

namespace Quantum\Libraries\Auth {

    function random_number(int $length = 10)
    {
        return 123456789;
    }

}

namespace Quantum\Tests\Libraries\Auth {

    use Quantum\Environment\Environment;
    use Quantum\Libraries\Auth\User;
    use Quantum\Libraries\Database\Sleekdb\SleekDbal;
    use Quantum\Tests\AppTestCase;
    use Quantum\Loader\Setup;
    use Quantum\Di\Di;
    use Quantum\App;
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
        protected $apiAuth;
        protected $authService;
        protected $mailer;
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
        protected $adminUser = [
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
        protected $guestUser = [
            'email' => 'guest@qt.com',
            'password' => '123456',
            'firstname' => 'Guest',
            'lastname' => 'User',
        ];

        public function setUp(): void
        {
            parent::setUp();

            config()->import(new Setup('config', 'database'));

            config()->set('database.current', 'sleekdb');

            SleekDbal::connect(config()->get('database.sleekdb'));

            Environment::getInstance()->load(new Setup('config', 'env'));

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
        }

    }

}