<?php

use Core\Authenticator;

if (Authenticator::check()) {
    redirect('/');
}
$errors = Core\Session::get('errors') ?? [];
$old = Core\Session::get('old') ?? [];

view('auth/login.php', [
    'errors' => $errors,
    'old' => $old,
]);
