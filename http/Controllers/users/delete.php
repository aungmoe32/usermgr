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
    $errors[] = "Invalid user ID.";
}

// Check if user exists
if (!$errors && $id) {
    $user = db()->query("SELECT id, name FROM users WHERE id = ?", [$id])->find();
    if (!$user) {
        $errors[] = "User not found.";
    }
}

if ($errors) {
    Session::flash('errors', $errors);
    redirect('/users');
    exit;
}

try {
    // Delete user
    db()->query("DELETE FROM users WHERE id = ?", [$id]);

    Session::flash('success', "User '{$user['name']}' has been deleted successfully!");
    redirect('/users');
} catch (Exception $e) {
    if (strpos($e->getMessage(), 'foreign key constraint') !== false) {
        Session::flash('errors', ['Cannot delete user. This user may be referenced by other records.']);
    } else {
        Session::flash('errors', ['Failed to delete user. Please try again.']);
    }
    redirect('/users');
}
