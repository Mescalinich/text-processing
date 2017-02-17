<?php

error_reporting(E_ALL);
ini_set("display_errors", 1);

require_once 'app/app.config.php';
require_once 'app/lib/autoloader.php';

$router = new App\Lib\Router(APP_ROUTES_CONFIG);
$router->run();