<?php

namespace Quantum\Tests\_root\modules\Test\Controllers;

use Quantum\Http\Response;

class TestController
{
    public function tests(Response $response): Response
    {
        return $response->html('');
    }
}
