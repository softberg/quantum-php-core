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

namespace Quantum\Captcha;

use Quantum\Captcha\Exceptions\CaptchaException;
use Quantum\Captcha\Contracts\CaptchaInterface;
use Quantum\App\Exceptions\BaseException;

/**
 * Class Captcha
 * @package Quantum\Captcha
 * @method string getName()
 * @method string|null getType()
 * @method CaptchaInterface setType(string $type)
 * @method mixed addToForm(string $formIdentifier)
 * @method bool verify(string $response)
 * @method string|null getErrorMessage()
 */
class Captcha
{
    private CaptchaInterface $adapter;

    public function __construct(CaptchaInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    public function getAdapter(): CaptchaInterface
    {
        return $this->adapter;
    }

    /**
     * @param array<mixed>|null $arguments
     * @return mixed
     * @throws BaseException
     */
    public function __call(string $method, ?array $arguments)
    {
        if (!method_exists($this->adapter, $method)) {
            throw CaptchaException::methodNotSupported($method, get_class($this->adapter));
        }

        return $this->adapter->$method(...$arguments);
    }
}
