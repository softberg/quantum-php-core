<?php

namespace Quantum\Handlers;

use Quantum\Libraries\ResourceCache\ViewCache;
use Quantum\Http\Response;

class ViewCacheHandler
{
    protected ViewCache $viewCache;

    public function __construct()
    {
        $this->viewCache = ViewCache::getInstance();
    }

    public function serveCachedView(string $uri, Response $response): bool
    {
        if ($this->viewCache->isEnabled() && $this->viewCache->exists($uri)) {
            $response->html($this->viewCache->get($uri));
            return true;
        }

        return false;
    }
}
