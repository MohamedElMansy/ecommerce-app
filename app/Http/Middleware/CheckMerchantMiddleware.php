<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckMerchantMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {   
        $auth_user=auth()->user();
        if (!$auth_user->is_merchant) {
            abort(403,'UNAUTHORIZED');
        }
        return $next($request);
    }

}
