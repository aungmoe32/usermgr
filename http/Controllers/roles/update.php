<?php

use Core\Session;

// Handle method override for PUT requests
$method = $_POST['_method'] ?? $_SERVER['REQUEST_METHOD'];

if ($method !== 'PUT') {
    http_response_code(405);
    echo "Method Not Allowed";
    exit;
}

$id = $_POST['id'] ?? null;
$name = trim($_POST['name'] ?? '');
$permissions = $_POST['permissions'] ?? [];

$errors = [];

// Validate ID
if (!$id || !is_numeric($id)) {
    $errors[] = "Invalid role ID.";
}

// Validate role name
if (empty($name)) {
    $errors['name'] = 'Role name is required';
} elseif (strlen($name) < 2) {
    $errors['name'] = 'Role name must be at least 2 characters long';
} elseif (strlen($name) > 50) {
    $errors['name'] = 'Role name must be 50 characters or less';
} elseif (!preg_match('/^[a-zA-Z\s]+$/', $name)) {
    $errors['name'] = 'Role name can only contain letters and spaces';
}

// Check if role exists
if (!$errors && $id) {
    $existingRole = db()->query("SELECT id, name FROM roles WHERE id = ?", [$id])->find();
    if (!$existingRole) {
        $errors[] = "Role not found.";
    }
}

// Check for duplicate name (excluding current role)
if (!$errors && $name && $id) {
    $duplicate = db()->query("SELECT id FROM roles WHERE LOWER(name) = LOWER(?) AND id != ?", [$name, $id])->find();
    if ($duplicate) {
        $errors['name'] = 'A role with this name already exists';
    }
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

if ($errors) {
    Session::flash('errors', $errors);
    Session::flash('old', [
        'name' => $name,
        'permissions' => $permissions
    ]);
    redirect("/roles/edit?id=$id");
}

try {
    db()->beginTransaction();

    db()->query(
        "UPDATE roles SET name = ?, updated_at = NOW() WHERE id = ?",
        [$name, $id]
    );

    db()->query("DELETE FROM roles_permission WHERE role_id = ?", [$id]);

    foreach ($permissions as $permissionId) {
        db()->query(
            "INSERT INTO roles_permission (role_id, permission_id) VALUES (?, ?)",
            [$id, $permissionId]
        );
    }

    db()->commit();

    Session::flash('success', "Role '{$name}' updated successfully with " . count($permissions) . " permission(s)!");
    redirect('/roles');
} catch (Exception $e) {
    db()->rollBack();
    Session::flash('errors', ['Failed to update role. Please try again.']);
    Session::flash('old', [
        'name' => $name,
        'permissions' => $permissions
    ]);
    redirect("/roles/edit?id=$id");
}
