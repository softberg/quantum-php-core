<?php

declare(strict_types=1);

/**
 * Quantum PHP Framework
 *
 * An open source software development framework for PHP
 *
 * @package Quantum
 * @author Arman Ag. <arman.ag@softberg.org>
 * @copyright Copyright (c) 2018 Softberg LLC (https://softberg.org)
 * @link http://quantum.softberg.org/
 * @since 3.0.0
 */

namespace Quantum\Http;

use Quantum\Http\Traits\Response\Header;
use Quantum\Http\Traits\Response\Status;
use Quantum\Http\Traits\Response\Body;
use Quantum\Environment\Environment;
use Quantum\Environment\Enums\Env;
use Quantum\Http\Enums\StatusCode;
use Exception;
use Quantum\Di\Di;

/**
 * Class Response
 * @package Quantum\Http
 */
class Response
{
    use Header;
    use Body;
    use Status;

    /**
     * XML root element
     * @var string
     */
    private string $xmlRoot = '<data></data>';

    /**
     * Callback function
     * @var string
     */
    private string $callbackFunction = '';

    /**
     * Flushes the response header and body
     */
    public function flush(): void
    {
        $this->__statusCode = StatusCode::OK;
        $this->__headers = [];
        $this->__response = [];
        $this->xmlRoot = '<data></data>';
        $this->callbackFunction = '';
    }

    /**
     * Sends all response data to the client and finishes the request.
     * @throws Exception
     */
    public function send(): void
    {
        if (Di::get(Environment::class)->getAppEnv() !== Env::TESTING) {
            while (ob_get_level() > 0) {
                ob_end_clean();
            }
        }

        foreach ($this->__headers as $key => $value) {
            header($key . ': ' . $value);
        }

        http_response_code($this->getStatusCode());

        echo $this->getContent();
    }
}
