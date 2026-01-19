<?php
$user_id = $_GET['id'] ?? null;

if (!$user_id || !is_numeric($user_id)) {
    header('Location: /users');
    exit;
}

$user = db()->query("
    SELECT 
        u.id,
        u.name,
        u.email,
        u.role_id,
        u.is_active
    FROM users u
    WHERE u.id = ?
", [$user_id])->find();

if (!$user) {
    header('Location: /users');
    exit;
}

$roles = db()->query("SELECT id, name FROM roles ORDER BY name")->get();

$errors = Core\Session::get('errors', []);
$success = Core\Session::get('success');
$old = Core\Session::get('old', []);

$name = $old['name'] ?? $user['name'];
$email = $old['email'] ?? $user['email'];
$role_id = $old['role_id'] ?? $user['role_id'];
$is_active = isset($old['is_active']) ? $old['is_active'] : $user['is_active'];

view('users/edit.php', [
    'user' => $user,
    'errors' => $errors,
    'old' => $old,
    'success' => $success,
    'roles' => $roles,
    'name' => $name,
    'email' => $email,
    'role_id' => $role_id,
    'is_active' => $is_active
]);
