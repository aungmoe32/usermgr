<?php
include("vendor/autoload.php");

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// an associative array: URI => file_to_include
$routes = [
    '/'         => 'views/home.php',
    '/admin/users'    => 'views/admin/users.php',
];

if (preg_match('/\.(?:png|jpg|jpeg|gif|css|js)$/', $_SERVER["REQUEST_URI"])) {
    return false;
}

if (array_key_exists($path, $routes)) {
    require $routes[$path];
} else {
    http_response_code(404);
    require 'views/404.php'; // A custom 404 page
}
