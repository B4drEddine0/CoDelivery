<?php

use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\ClientMiddleware;
use App\Http\Middleware\LivreurMiddleware;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Http\Middleware\HandleCors;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

return [
    // Global middleware
    'web' => [
        \App\Http\Middleware\EncryptCookies::class,
        \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
        \Illuminate\Session\Middleware\StartSession::class,
        ShareErrorsFromSession::class,
        ValidateCsrfToken::class,
        SubstituteBindings::class,
    ],

    'api' => [
        HandleCors::class,
        SubstituteBindings::class,
    ],

    // Named middleware
    'auth' => \App\Http\Middleware\Authenticate::class,
    'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
    'auth.session' => AuthenticateSession::class,
    'client' => ClientMiddleware::class,
    'livreur' => LivreurMiddleware::class,
    'admin' => AdminMiddleware::class,
];
