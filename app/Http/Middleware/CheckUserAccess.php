<?php

namespace App\Http\Middleware;

use App\Exceptions\UserAccessError;
use Closure;
use Illuminate\Http\Request;

class CheckUserAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if($request->json('user_type') == 1)
        return $next($request);
        else
        throw new UserAccessError();
    }
}
