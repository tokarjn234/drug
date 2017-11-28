<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\DebugLog;
use Mockery\Exception;

class DebugLogMiddleware
{

    public function handle($request, Closure $next)
    {
        $response = $next($request);

        try {
//            $entry = DebugLog::init();
//            $entry->message = 'ApiDebug';
//            $entry->event_type = 'info';
//            $entry->response = json_encode($response->original);
//            $entry->save();
        } catch (\Exception $e) {

        }

        return $response;
    }
}