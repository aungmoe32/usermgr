<?php

use Core\CsrfTokenException;
use Core\Session;

session_start();

define('BASE_PATH', dirname(__DIR__) . '/');

require_once BASE_PATH . '/vendor/autoload.php';
require_once BASE_PATH . '/core/helper.php';

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = strtoupper($_SERVER['REQUEST_METHOD']);


require_once BASE_PATH . 'routes.php';

try {
    $router->route($uri, $method);
} catch (CsrfTokenException $exception) {
    Session::flash('errors', ["Page expired.", "Please refresh the page"]);
    return redirect(previousUrl());
}


Session::unflash();
