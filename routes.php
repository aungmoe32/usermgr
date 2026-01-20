<?php

use Core\Router;

$router = new Router();

$router->get('/', 'home.php');

// Authentication
$router->get('/login', 'auth/loginForm.php');
$router->post('/login', 'auth/login.php');
$router->post('/logout', 'auth/logout.php');

// Users
$router->get('/users', 'users/index.php', ['authenticated']);
$router->get('/users/create', 'users/create.php');
$router->post('/users/store', 'users/store.php');
$router->get('/users/edit', 'users/edit.php');
$router->post('/users/update', 'users/update.php');
$router->post('/users/delete', 'users/delete.php');

// Roles
$router->get('/roles', 'roles/index.php');
$router->get('/roles/create', 'roles/create.php');
$router->post('/roles/store', 'roles/store.php');
$router->get('/roles/edit', 'roles/edit.php');
$router->post('/roles/update', 'roles/update.php');
$router->post('/roles/delete', 'roles/delete.php');
