<?php

use Core\Session;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo "Method Not Allowed";
    exit;
}

$name = trim($_POST['name'] ?? '');

$errors = [];

// Validate role name
if (empty($name)) {
    $errors['name'] = 'Role name is required';
} elseif (strlen($name) < 2) {
    $errors['name'] = 'Role name must be at least 2 characters long';
} elseif (strlen($name) > 100) {
    $errors['name'] = 'Role name must be 100 characters or less';
} elseif (!preg_match('/^[a-zA-Z\s]+$/', $name)) {
    $errors['name'] = 'Role name can only contain letters and spaces';
}


// Check if role name already exists
if (empty($errors['name'])) {
    $existingRole = db()->query("SELECT id FROM roles WHERE LOWER(name) = LOWER(?)", [$name])->find();
    if ($existingRole) {
        $errors['name'] = 'A role with this name already exists';
    }
}

if ($errors) {
    Session::flash('errors', $errors);
    Session::flash('old', $_POST);
    redirect('/roles/create');
    exit;
}

try {
    // Insert new role
    db()->query(
        "INSERT INTO roles (name) VALUES (?)",
        [$name]
    );

    Session::flash('success', "Role '{$name}' created successfully!");
    redirect('/roles');
} catch (Exception $e) {
    Session::flash('errors', ['Failed to create role. Please try again.']);
    Session::flash('old', $_POST);
    redirect('/roles/create');
}