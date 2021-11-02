<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\Auth;
use Closure;

class CheckSessionID
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(Auth::user()->session_id != $request->getSession()->getId()){
            Auth::logout();
            $request->session()->flush();
            return redirect()->route('login_again');
        }
        return $next($request);
    }
}
