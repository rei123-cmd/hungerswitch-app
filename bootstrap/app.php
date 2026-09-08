<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

$_ENV['APP_SERVICES_CACHE'] = $_ENV['APP_SERVICES_CACHE'] ?? '/tmp/services.php';
$_ENV['APP_PACKAGES_CACHE'] = $_ENV['APP_PACKAGES_CACHE'] ?? '/tmp/packages.php';
$_ENV['APP_CONFIG_CACHE'] = $_ENV['APP_CONFIG_CACHE'] ?? '/tmp/config.php';
$_ENV['VIEW_COMPILED_PATH'] = $_ENV['VIEW_COMPILED_PATH'] ?? '/tmp';

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();