<?php

$success = Core\Session::get('success');
$errors = Core\Session::get('errors', []);

// Fetch all roles with user count and permissions
$roles = db()->query("
    SELECT 
        r.id,
        r.name,
        r.created_at,
        COUNT(DISTINCT u.id) as user_count
    FROM roles r
    LEFT JOIN users u ON r.id = u.role_id
    GROUP BY r.id, r.name, r.created_at
    ORDER BY r.created_at DESC
")->get();

// Fetch permissions for each role
foreach ($roles as &$role) {
    $permissions = db()->query("
        SELECT 
            p.name as permission_name,
            f.name as feature_name
        FROM roles_permission rp
        JOIN permissions p ON rp.permission_id = p.id
        JOIN features f ON p.feature_id = f.id
        WHERE rp.role_id = ?
        ORDER BY f.name, p.name
    ", [$role['id']])->get();

    $role['permissions'] = $permissions;
    $role['permission_count'] = count($permissions);
}


view("roles/list.php", [
    'roles' => $roles,
    'errors' => $errors,
    'success' => $success
]);
