<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirmation = $_POST['password_confirmation'] ?? '';
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

    // Validate password
    if (empty($password)) {
        $errors['password'] = 'Password is required';
    } elseif (strlen($password) < 8) {
        $errors['password'] = 'Password must be at least 8 characters long';
    } elseif (strlen($password) > 255) {
        $errors['password'] = 'Password must be 255 characters or less';
    }

    // Validate password confirmation
    if (empty($password_confirmation)) {
        $errors['password_confirmation'] = 'Password confirmation is required';
    } elseif ($password !== $password_confirmation) {
        $errors['password_confirmation'] = 'Passwords do not match';
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
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

            db()->query(
                "INSERT INTO users (name, email, password, role_id) VALUES (?, ?, ?, ?)",
                [$name, $email, $hashedPassword, $role_id]
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
