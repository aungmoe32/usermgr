<?php

use Core\Session;

$method = $_POST['_method'] ?? $_SERVER['REQUEST_METHOD'];

if ($method !== 'PUT') {
    http_response_code(405);
    echo "Method Not Allowed";
    exit;
}

$id = $_POST['id'] ?? null;
$name = trim($_POST['name'] ?? '');
$role_id = $_POST['role_id'] ?? null;
$is_active = isset($_POST['is_active']) && $_POST['is_active'] !== '' ? (int)$_POST['is_active'] : 0;
$errors = [];

// Validate ID
if (!$id || !is_numeric($id)) {
    $errors[] = "Invalid user ID.";
}

// Validate name
if (empty($name)) {
    $errors[] = "Name is required.";
} elseif (strlen($name) < 2) {
    $errors[] = "Name must be at least 2 characters long.";
} elseif (strlen($name) > 255) {
    $errors[] = "Name cannot exceed 255 characters.";
}

// Check if user exists
if (!$errors && $id) {
    $existingUser = db()->query("SELECT id FROM users WHERE id = ?", [$id])->find();
    if (!$existingUser) {
        $errors[] = "User not found.";
    }
}

// Check for duplicate name
if (!$errors && $name && $id) {
    $duplicate = db()->query("SELECT id FROM users WHERE name = ? AND id != ?", [$name, $id])->find();
    if ($duplicate) {
        $errors[] = "A user with this name already exists.";
    }
}

// Validate role_id
if (!$errors) {
    if (empty($role_id)) {
        $errors[] = "Role is required.";
    } else {
        $role = db()->query("SELECT id FROM roles WHERE id = ?", [$role_id])->find();
        if (!$role) {
            $errors[] = "Invalid role selected.";
        }
    }
}

// Validate is_active
if (!isset($_POST['is_active']) || $_POST['is_active'] === '') {
    $errors[] = "User status is required.";
}

if ($errors) {
    Session::flash('errors', $errors);
    Session::flash('old', [
        'name' => $name,
        'role_id' => $role_id,
        'is_active' => $is_active
    ]);
    redirect("/users/edit?id=$id");
}

try {

    // Update user
    db()->query(
        "UPDATE users SET name = ?, role_id = ?, is_active = ?, updated_at = NOW() WHERE id = ?",
        [$name, $role_id, $is_active, $id]
    );

    Session::flash('success', 'User updated successfully!');
    redirect('/users');
} catch (Exception $e) {
    Session::flash('errors', ['Failed to update user. Please try again.']);
    Session::flash('old', [
        'name' => $name,
        'role_id' => $role_id,
        'is_active' => $is_active
    ]);
    redirect("/users/edit?id=$id");
}
