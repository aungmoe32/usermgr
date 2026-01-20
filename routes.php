<?php

use Core\Router;

$router = new Router();

$router->get('/', 'home.php');

// Authentication
$router->get('/login', 'auth/loginForm.php');
$router->post('/login', 'auth/login.php');
$router->post('/logout', 'auth/logout.php', ['authenticated']);

// Users
$router->get('/users', 'users/index.php', ['authenticated']);
$router->get('/users/create', 'users/create.php', ['authenticated']);
$router->post('/users/store', 'users/store.php', ['authenticated']);
$router->get('/users/edit', 'users/edit.php', ['authenticated']);
$router->post('/users/update', 'users/update.php', ['authenticated']);
$router->post('/users/delete', 'users/delete.php', ['authenticated']);

// Roles
$router->get('/roles', 'roles/index.php', ['authenticated']);
$router->get('/roles/create', 'roles/create.php', ['authenticated']);
$router->post('/roles/store', 'roles/store.php', ['authenticated']);
$router->get('/roles/edit', 'roles/edit.php', ['authenticated']);
$router->post('/roles/update', 'roles/update.php', ['authenticated']);
$router->post('/roles/delete', 'roles/delete.php', ['authenticated']);
