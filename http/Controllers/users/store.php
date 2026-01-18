<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $role_id = trim($_POST['role_id'] ?? '');

    $errors = [];

    // Validate name
    if (empty($name)) {
        $errors['name'] = 'Name is required';
    } elseif (strlen($name) > 100) {
        $errors['name'] = 'Name must be 100 characters or less';
    }

    // Validate email
    if (empty($email)) {
        $errors['email'] = 'Email is required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Please enter a valid email address';
    } elseif (strlen($email) > 255) {
        $errors['email'] = 'Email must be 255 characters or less';
    }

    // Validate role_id
    if (empty($role_id)) {
        $errors['role_id'] = 'Role is required';
    } elseif (!in_array($role_id, ['1', '2', '3'])) {
        $errors['role_id'] = 'Invalid role selected';
    }


    // Check email already exists
    if (empty($errors['email'])) {
        $existingEmail = db()->query("SELECT id FROM users WHERE email = ?", [$email])->find();
        if ($existingEmail) {
            $errors['email'] = 'A user with this email already exists';
        }
    }

    // If validation passes, create the user
    if (empty($errors)) {
        try {
            db()->query(
                "INSERT INTO users (name, email, role_id) VALUES (?, ?, ?)",
                [$name, $email, $role_id]
            );
            Core\Session::flash('success', "User created successfully");
            redirect('/users');
        } catch (Exception $e) {
            $errors['general'] = 'Failed to create user. Please try again.';
        }
    }

    // redirect back with form data if error
    if (!empty($errors)) {
        Core\Session::flash('errors', $errors);
        Core\Session::flash('old', $_POST);
        redirect('/users/create');
    }
} else {
    // If not POST request, redirect to create form
    redirect('/users/create');
}
