<?php

namespace Quantum\Tests\_root\shared\Models;

use Quantum\Model\QtModel;

class User extends QtModel
{

    public $idColumn = 'id';

    public $table = 'users';

    public $fillable = [
        'uuid',
        'firstname',
        'lastname',
        'role',
        'email',
        'password',
        'activation_token',
        'remember_token',
        'reset_token',
        'access_token',
        'refresh_token',
        'otp',
        'otp_expires',
        'otp_token',
    ];
}
