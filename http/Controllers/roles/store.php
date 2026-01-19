<?php

use Core\Session;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo "Method Not Allowed";
    exit;
}

$name = trim($_POST['name'] ?? '');
$permissions = $_POST['permissions'] ?? [];

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

// Validate permissions
if (empty($permissions)) {
    $errors['permissions'] = 'Please select at least one permission for this role';
} else {
    // Validate that all selected permissions exist
    $placeholders = implode(',', array_fill(0, count($permissions), '?'));
    $validPermissions = db()->query("SELECT id FROM permissions WHERE id IN ({$placeholders})", $permissions)->get();
    if (count($validPermissions) !== count($permissions)) {
        $errors['permissions'] = 'Invalid permissions selected';
    }
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
    db()->beginTransaction();

    db()->query(
        "INSERT INTO roles (name) VALUES (?)",
        [$name]
    );

    $roleId = db()->lastInsertId();

    foreach ($permissions as $permissionId) {
        db()->query(
            "INSERT INTO roles_permission (role_id, permission_id) VALUES (?, ?)",
            [$roleId, $permissionId]
        );
    }

    db()->commit();

    Session::flash('success', "Role '{$name}' created successfully with " . count($permissions) . " permission(s)!");
    redirect('/roles');
} catch (Exception $e) {
    // Rollback on error
    db()->rollBack();
    Session::flash('errors', ['Failed to create role. Please try again.']);
    Session::flash('old', $_POST);
    redirect('/roles/create');
}
