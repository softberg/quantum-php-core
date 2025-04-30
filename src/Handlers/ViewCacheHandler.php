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

/**
 * Serves a cached view if it exists and caching is enabled.
 *
 * @param string $uri The URI of the view to serve.
 * @param Response $response The Response object to send the cached view to.
 * @return bool True if a cached view was served, false otherwise.
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
