<?php

namespace Module\Vendor\Middleware;

class StatelessRouteMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, \Closure $next)
    {
        config()->set('session.driver', 'array');
        return $next($request);
    }

}