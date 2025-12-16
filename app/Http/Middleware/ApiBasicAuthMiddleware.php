<?php

namespace App\Http\Middleware;
use Closure;

class ApiBasicAuthMiddleware
{
    public function handle($request, Closure $next)
    {
        $username = config('srboard.api.username');
        $password = config('srboard.api.password');

        if ($request->getUser() !== $username || $request->getPassword() !== $password){
            $headers = ['WWW-Authenticate' => 'Basic'];
            return response('Unauthorized', 401, $headers);
        }
        return $next($request);
    }
}
