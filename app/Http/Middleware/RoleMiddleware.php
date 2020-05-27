<?php

namespace App\Http\Middleware;

use Closure;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $roles = 'admin')
    {
        $user_roles = auth()->user()->roles;
        if($roles === 'all' && count($user_roles) !== 0){
            return $next($request);
        }
        $roles = explode(',',$roles);
        foreach($user_roles as $user_role){
            if($user_role->name === 'admin' || $user_role->name === 'system'){
                return $next($request);
            }
            foreach($roles as $role){
                if($role === $user_role->name){
                    return $next($request);
                }
            }
        }
        return response()->json('Icazeniz yoxdur [role]');
    }
}
