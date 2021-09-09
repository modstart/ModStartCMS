<?php

namespace Module\Vendor\Middleware;

class StatelessRouteMiddleware
{
    
    public function handle($request, \Closure $next)
    {
        config()->set('session.driver', 'array');
        return $next($request);
    }

}