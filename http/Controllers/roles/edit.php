<?php

$role_id = $_GET['id'] ?? null;

if (!$role_id || !is_numeric($role_id)) {
    header('Location: /roles');
    exit;
}

// Fetch role data
$role = db()->query("SELECT id, name FROM roles WHERE id = ?", [$role_id])->find();

if (!$role) {
    redirect('/roles');
}

$errors = Core\Session::get('errors', []);
$success = Core\Session::get('success');
$old = Core\Session::get('old', []);

// Use old values if available, otherwise use role data
$name = $old['name'] ?? $role['name'];
$selectedPermissions = $old['permissions'] ?? [];

if (empty($selectedPermissions) && empty($old)) {
    $currentPermissions = db()->query("
        SELECT permission_id 
        FROM roles_permission 
        WHERE role_id = ?
    ", [$role_id])->get();
    $selectedPermissions = array_column($currentPermissions, 'permission_id');
}

// Fetch all features and permissions
$features = db()->query("
    SELECT 
        f.id,
        f.name,
        f.description,
        p.id as permission_id,
        p.name as permission_name,
        p.description as permission_description
    FROM features f
    JOIN permissions p ON f.id = p.feature_id
    ORDER BY f.name, p.name
")->get();

// Group permissions by feature
$groupedPermissions = [];
foreach ($features as $feature) {
    $groupedPermissions[$feature['name']][] = [
        'id' => $feature['permission_id'],
        'name' => $feature['permission_name'],
        'description' => $feature['permission_description']
    ];
}
view('roles/edit.php', [
    'errors' => $errors,
    'success' => $success,
    'name' => $name,
    'selectedPermissions' => $selectedPermissions,
    'groupedPermissions' => $groupedPermissions,
    'role_id' => $role_id,
    'role' => $role
]);
