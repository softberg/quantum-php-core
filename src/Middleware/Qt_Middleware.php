<?php


namespace Quantum\Middleware;

use Quantum\Http\Request;

abstract class Qt_Middleware {
    
    abstract protected function apply(Request $request, \Closure $next);

}
