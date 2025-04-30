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
 * @since 2.9.7
 */

namespace Quantum\Handlers;

use Quantum\Libraries\Database\Exceptions\DatabaseException;
use Quantum\Libraries\Session\Exceptions\SessionException;
use Quantum\Libraries\Config\Exceptions\ConfigException;
use Quantum\Libraries\ResourceCache\ViewCache;
use Quantum\Di\Exceptions\DiException;
use Quantum\Exceptions\BaseException;
use Quantum\Http\Response;
use ReflectionException;

class ViewCacheHandler
{

    /**
     * @var ViewCache
     */
    protected $viewCache;

    public function __construct()
    {
        $this->viewCache = ViewCache::getInstance();
    }

    /**
     * Serves a cached view if it exists and caching is enabled.
     * @param string $uri
     * @param Response $response
     * @return bool
     * @throws DiException
     * @throws BaseException
     * @throws ConfigException
     * @throws DatabaseException
     * @throws SessionException
     * @throws ReflectionException
     */
    public function serveCachedView(string $uri, Response $response): bool
    {
        if ($this->viewCache->isEnabled() && $this->viewCache->exists($uri)) {
            $response->html($this->viewCache->get($uri));
            return true;
        }

        return false;
    }
}
