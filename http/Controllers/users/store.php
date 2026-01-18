<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $role_id = trim($_POST['role_id'] ?? '');

    $errors = [];

    // Validate name
    if (empty($name)) {
        $errors['name'] = 'Name is required';
    } elseif (strlen($name) > 100) {
        $errors['name'] = 'Name must be 100 characters or less';
    }

    // Validate role_id
    if (empty($role_id)) {
        $errors['role_id'] = 'Role is required';
    } elseif (!in_array($role_id, ['1', '2', '3'])) {
        $errors['role_id'] = 'Invalid role selected';
    }

    // Check if name already exists
    if (empty($errors['name'])) {
        $existingUser = db()->query("SELECT id FROM users WHERE name = ?", [$name])->find();
        if ($existingUser) {
            $errors['name'] = 'A user with this name already exists';
        }
    }

    // If validation passes, create the user
    if (empty($errors)) {
        try {
            db()->query(
                "INSERT INTO users (name, role_id) VALUES (?, ?)",
                [$name, $role_id]
            );

            // Success - redirect to users list or show success message
            redirect('/users/create?success=User created successfully');
        } catch (Exception $e) {
            $errors['general'] = 'Failed to create user. Please try again.';
        }
    }

    // If there are errors, store them and redirect back with form data
    if (!empty($errors)) {
        Core\Session::flash('errors', $errors);
        Core\Session::flash('old', $_POST);
        redirect('/users/create');
    }
} else {
    // If not POST request, redirect to create form
    redirect('/users/create');
}
