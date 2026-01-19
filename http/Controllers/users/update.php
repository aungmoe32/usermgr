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
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$password_confirmation = $_POST['password_confirmation'] ?? '';
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
} elseif (strlen($name) > 100) {
    $errors[] = "Name cannot exceed 100 characters.";
}

// Validate email
if (empty($email)) {
    $errors[] = "Email is required.";
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Please enter a valid email address.";
} elseif (strlen($email) > 255) {
    $errors[] = "Email cannot exceed 255 characters.";
}

// Check if user exists
if (!$errors && $id) {
    $existingUser = db()->query("SELECT id FROM users WHERE id = ?", [$id])->find();
    if (!$existingUser) {
        $errors[] = "User not found.";
    }
}


// Check for duplicate email (excluding current user)
if (!$errors && $email && $id) {
    $duplicate = db()->query("SELECT id FROM users WHERE email = ? AND id != ?", [$email, $id])->find();
    if ($duplicate) {
        $errors[] = "A user with this email already exists.";
    }
}

// Validate password (only if provided)
if (!empty($password) || !empty($password_confirmation)) {
    if (empty($password)) {
        $errors[] = "Password is required when changing password.";
    } elseif (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters long.";
    } elseif (strlen($password) > 255) {
        $errors[] = "Password must be 255 characters or less.";
    }

    if (empty($password_confirmation)) {
        $errors[] = "Password confirmation is required when changing password.";
    } elseif ($password !== $password_confirmation) {
        $errors[] = "Passwords do not match.";
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
        'email' => $email,
        'role_id' => $role_id,
        'is_active' => $is_active
    ]);
    redirect("/users/edit?id=$id");
}

try {
    if (!empty($password)) {
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        db()->query(
            "UPDATE users SET name = ?, email = ?, password = ?, role_id = ?, is_active = ?, updated_at = NOW() WHERE id = ?",
            [$name, $email, $hashedPassword, $role_id, $is_active, $id]
        );
    } else {
        db()->query(
            "UPDATE users SET name = ?, email = ?, role_id = ?, is_active = ?, updated_at = NOW() WHERE id = ?",
            [$name, $email, $role_id, $is_active, $id]
        );
    }

    Session::flash('success', 'User updated successfully!');
    redirect('/users');
} catch (Exception $e) {
    Session::flash('errors', ['Failed to update user. Please try again.']);
    Session::flash('old', [
        'name' => $name,
        'email' => $email,
        'role_id' => $role_id,
        'is_active' => $is_active
    ]);
    redirect("/users/edit?id=$id");
}
