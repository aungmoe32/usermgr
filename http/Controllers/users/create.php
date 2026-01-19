<?php

$errors = Core\Session::get('errors') ?? [];
$old = Core\Session::get('old') ?? [];
$success = $_GET['success'] ?? null;

// Fetch roles 
$roles = db()->query("SELECT id, name FROM roles ORDER BY name")->get();

view('users/create.php', [
    'errors' => $errors,
    'old' => $old,
    'success' => $success,
    'roles' => $roles
]);
