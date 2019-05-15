<?php


namespace Quantum\Middleware;

abstract class Qt_Middleware {
    
    abstract protected function apply($request, \Closure $next);

}
