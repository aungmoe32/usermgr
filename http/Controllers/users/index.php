<?php

$success = Core\Session::get('success');
$errors = Core\Session::get('errors', []);

// Fetch all users with their roles
$users = db()->query("
    SELECT 
        u.id,
        u.name,
        u.email,
        u.role_id,
        u.is_active,
        u.created_at,
        r.name AS role_name
    FROM users u
    JOIN roles r ON u.role_id = r.id
    ORDER BY u.created_at DESC
")->get();

view("users/list.php", [
    'users' => $users,
    'errors' => $errors,
    'success' => $success
]);
