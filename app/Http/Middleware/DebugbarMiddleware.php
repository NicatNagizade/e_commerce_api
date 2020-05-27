<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;

class DebugbarMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $type = null)
    {
        $response = $next($request);

        if (
            $response instanceof JsonResponse &&
            app()->bound('debugbar') &&
            app('debugbar')->isEnabled() &&
            is_object($response->getData())
        ) {
            $debugbar = [];
            if($type === 'sql'){
                $debugbar = collect(app('debugbar')->getData()['queries']['statements'])->pluck('sql','duration_str')->toArray();
            }else{
                $debugbar = app('debugbar')->getData();
            }
            $response->setData($response->getData(true) + [
                '_debugbar' => $debugbar,
            ]);
        }

        return $response;
    }
}
