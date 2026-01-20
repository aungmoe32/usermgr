<?php

use Core\Authenticator;
use Core\Session;

if (Authenticator::check()) {
    redirect('/');
}

$errors = Session::get('errors', []);
$old = Session::get('old', []);

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

$errors = [];

if (empty($email)) {
    $errors[] = 'Email address is required.';
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Please enter a valid email address.';
}

if (empty($password)) {
    $errors[] = 'Password is required.';
}

if (empty($errors)) {
    $result = Authenticator::attempt($email, $password);

    if ($result === true) {

        Session::flash('success', 'Welcome back, ' . Authenticator::user()['name'] . '!');
        redirect('/');
    } else {

        $errors[] = 'Invalid email address or password.';
    }
}

if (!empty($errors)) {
    Session::flash('errors', $errors);
    Session::flash('old', ['email' => $email]);
    redirect('/login');
}
