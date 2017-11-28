<?php

namespace App\Http;
require_once app_path('Lib/Functions.php');
use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * @var array
     */
    protected $middleware = [
        \Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode::class,
        \App\Http\Middleware\EncryptCookies::class,
        \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
        \Illuminate\Session\Middleware\StartSession::class,
        \Illuminate\View\Middleware\ShareErrorsFromSession::class,
        \App\Http\Middleware\VerifyCsrfToken::class,
        \App\Http\Middleware\AfterMiddleware::class
    ];

    /**
     * The application's route middleware.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'StoreAuth' => \App\Http\Middleware\StoreAuth::class,
        'CompanyAuth' => \App\Http\Middleware\CompanyAuth::class,
        'MediaidAuth' => \App\Http\Middleware\MediaidAuth::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'ApiMiddleware' => \App\Http\Middleware\ApiMiddleware::class,
        'WinApi' => \App\Http\Middleware\WinApi::class
    ];
}
