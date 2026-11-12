<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Normalize subfolder routing when URL rewrites bypass /public (e.g. /ems/employee/login)
if (isset($_SERVER['SCRIPT_NAME']) && str_contains($_SERVER['SCRIPT_NAME'], '/public/index.php')) {
    $scriptDir = dirname($_SERVER['SCRIPT_NAME']);
    $parentDir = dirname($scriptDir);
    if ($parentDir === '.' || $parentDir === DIRECTORY_SEPARATOR) {
        $parentDir = '';
    }
    if (isset($_SERVER['REQUEST_URI']) && !str_contains($_SERVER['REQUEST_URI'], '/public/')) {
        $_SERVER['SCRIPT_NAME'] = ($parentDir ? $parentDir : '') . '/index.php';
        $_SERVER['PHP_SELF'] = $_SERVER['SCRIPT_NAME'];

        if ($_SERVER['REQUEST_URI'] === $parentDir) {
            $_SERVER['REQUEST_URI'] = $parentDir . '/';
        }
    }
}

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

$app->handleRequest(Request::capture());
