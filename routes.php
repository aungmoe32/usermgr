<?php
$routes = [
    'GET' => [
        '/' => 'views/home.php',
        '/users' => 'views/users/list.php',
        '/users/create' => 'views/users/create.php',
    ],
    'POST' => [
        '/users/store' => 'http/Controllers/users/store.php',
    ],
    'PUT' => [],
    'DELETE' => []
];
