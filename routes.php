<?php
$routes = [
    'GET' => [
        '/' => 'views/home.php',
        '/admin/users' => 'views/admin/users.php',
    ],
    'POST' => [
        '/users/create' => 'http/Controllers/users/create.php',
    ],
    'PUT' => [],
    'DELETE' => []
];
