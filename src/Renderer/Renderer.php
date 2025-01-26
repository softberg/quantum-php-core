<?php

/**
 * Quantum PHP Framework
 *
 * An open source software development framework for PHP
 *
 * @package Quantum
 * @author Arman Ag. <arman.ag@softberg.org>
 * @copyright Copyright (c) 2018 Softberg LLC (https://softberg.org)
 * @link http://quantum.softberg.org/
 * @since 2.9.5
 */

namespace Quantum\Renderer;

use Quantum\Renderer\Contracts\TemplateRendererInterface;
use Quantum\Renderer\Exceptions\RendererException;
use Quantum\Exceptions\BaseException;

/**
 * Class Renderer
 * @package Quantum\Renderer
 * @method string render(string $view, array $params = [])
 */
class Renderer
{

    /**
     * @var TemplateRendererInterface
     */
    private $adapter;

    /**
     * @param TemplateRendererInterface $adapter
     */
    public function __construct(TemplateRendererInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * @return TemplateRendererInterface
     */
    public function getAdapter(): TemplateRendererInterface
    {
        return $this->adapter;
    }

    /**
     * @param string $method
     * @param array|null $arguments
     * @return mixed
     * @throws BaseException
     */
    public function __call(string $method, ?array $arguments)
    {
        if (!method_exists($this->adapter, $method)) {
            throw RendererException::methodNotSupported($method, get_class($this->adapter));
        }

        return $this->adapter->$method(...$arguments);
    }
}