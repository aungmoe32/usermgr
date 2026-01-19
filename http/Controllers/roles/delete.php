<?php

use Core\Session;

// Handle method override for DELETE requests
$method = $_POST['_method'] ?? $_SERVER['REQUEST_METHOD'];

if ($method !== 'DELETE') {
    http_response_code(405);
    echo "Method Not Allowed";
    exit;
}

$id = $_POST['id'] ?? null;

$errors = [];

// Validate ID
if (!$id || !is_numeric($id)) {
    $errors[] = "Invalid role ID.";
}

// Check if role exists
if (!$errors && $id) {
    $role = db()->query("SELECT id, name FROM roles WHERE id = ?", [$id])->find();
    if (!$role) {
        $errors[] = "Role not found.";
    }
}

// Check if role has users assigned (prevent deletion)
if (!$errors && $id) {
    $usersWithRole = db()->query("SELECT COUNT(*) as count FROM users WHERE role_id = ?", [$id])->find();
    if ($usersWithRole['count'] > 0) {
        $errors[] = "Cannot delete role. This role is assigned to {$usersWithRole['count']} user(s). Please reassign users to other roles first.";
    }
}

if ($errors) {
    Session::flash('errors', $errors);
    redirect('/roles');
    exit;
}

try {
    db()->beginTransaction();

    db()->query("DELETE FROM roles_permission WHERE role_id = ?", [$id]);

    db()->query("DELETE FROM roles WHERE id = ?", [$id]);

    db()->commit();

    Session::flash('success', "Role '{$role['name']}' has been deleted successfully!");
    redirect('/roles');
} catch (Exception $e) {
    db()->rollBack();

    if (strpos($e->getMessage(), 'foreign key constraint') !== false || strpos($e->getMessage(), 'violates foreign key') !== false) {
        Session::flash('errors', ['Cannot delete role. This role may be referenced by other records in the system.']);
    } else {
        Session::flash('errors', ['Failed to delete role. Please try again.']);
    }
    redirect('/roles');
}
