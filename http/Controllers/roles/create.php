<?php

$errors = Core\Session::get('errors', []);
$success = Core\Session::get('success');
$old = Core\Session::get('old', []);

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

view('roles/create.php', [
    'errors' => $errors,
    'success' => $success,
    'old' => $old,
    'groupedPermissions' => $groupedPermissions
]);
