<?php

use Core\CsrfTokenException;
use Core\Session;

session_start();

const BASE_PATH = __DIR__ . '/';
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/core/helper.php';

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = strtoupper($_SERVER['REQUEST_METHOD']);

// for assets
if (preg_match('/\.(?:png|jpg|jpeg|gif|css|js)$/', $_SERVER["REQUEST_URI"])) {
    return false;
}

require_once BASE_PATH . 'routes.php';

try {
    $router->route($uri, $method);
} catch (CsrfTokenException $exception) {
    Session::flash('errors', ["Page expired.", "Please refresh the page"]);
    return redirect(previousUrl());
}


Session::unflash();
