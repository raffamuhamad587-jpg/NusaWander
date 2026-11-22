u<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader if available, or fallback to root landing page
if (file_exists(__DIR__.'/../vendor/autoload.php')) {
    require __DIR__.'/../vendor/autoload.php';

    // Bootstrap Laravel and handle the request...
    (require_once __DIR__.'/../bootstrap/app.php')
        ->handleRequest(Request::capture());
} else {
    // Fallback directly to the standalone Modern Tropical Editorial landing page
    require_once __DIR__.'/../../index.php';
}
