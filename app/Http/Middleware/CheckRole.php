<?php

namespace App\Http\Middleware;


use Illuminate\Http\Request;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\AuthenticationException;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = Auth::guard('sanctum')->user();
    // dd(in_array($user->role, $roles),$user);
        if (!$user) {
            throw new AuthenticationException('Please login');
        }

        if (!in_array($user->role, $roles)) {
            throw new AuthenticationException('Dont have permission!!');
        }

        //set user to request
        $request->setUserResolver(fn () => $user);

        //set user to auth facade
        Auth::setUser($user);
        return $next($request);
    }
}
