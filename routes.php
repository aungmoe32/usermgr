<?php
$routes = [
    'GET' => [
        '/' => 'views/home.php',
        '/users' => 'views/users/list.php',
        '/users/create' => 'views/users/create.php',
        '/users/edit' => 'views/users/edit.php',
        '/roles' => 'views/roles/list.php',
        '/roles/create' => 'views/roles/create.php',
        '/roles/edit' => 'views/roles/edit.php',
    ],
    'POST' => [
        '/users/store' => 'http/Controllers/users/store.php',
        '/users/update' => 'http/Controllers/users/update.php',
        '/users/delete' => 'http/Controllers/users/delete.php',
        '/roles/store' => 'http/Controllers/roles/store.php',
        '/roles/update' => 'http/Controllers/roles/update.php',
    ],
];
