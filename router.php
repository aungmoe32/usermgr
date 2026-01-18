<?php

require_once __DIR__ . '/vendor/autoload.php';

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = strtoupper($_SERVER['REQUEST_METHOD']);

// for assets
if (preg_match('/\.(?:png|jpg|jpeg|gif|css|js)$/', $_SERVER["REQUEST_URI"])) {
    return false;
}

require_once __DIR__ . '/routes.php';

if (array_key_exists($method, $routes) && array_key_exists($uri, $routes[$method])) {
    require $routes[$method][$uri];
} else {
    // Handle 404 Not Found
    http_response_code(404);
    echo "<h1>404 Not Found</h1>";
}
