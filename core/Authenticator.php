<?php

namespace Core;

class Authenticator
{

    public static function attempt($email, $password)
    {
        $user = db()
            ->query('SELECT users.*, roles.name as role_name FROM users LEFT JOIN roles ON users.role_id = roles.id WHERE email = ? AND is_active = true', [
                $email
            ])->find();

        if ($user) {
            if (!password_verify($password, $user['password'])) {
                return 'invalid_credentials';
            }

            self::login($user);
            return true;
        }

        return 'invalid_credentials';
    }

    public static function login($user)
    {
        $_SESSION['user'] = [
            'id' => $user['id'],
            'email' => $user['email'],
            'name' => $user['name'],
            'role_id' => $user['role_id'],
            'role_name' => $user['role_name'],
            'is_active' => $user['is_active']
        ];

        session_regenerate_id(true);
    }

    public static function logout()
    {
        Session::destroy();
    }

    public static function user()
    {
        return $_SESSION['user'] ?? null;
    }


    public static function check()
    {
        return isset($_SESSION['user']);
    }

    public static function guest()
    {
        return !self::check();
    }

    public static function onlyGuest()
    {
        if (!self::guest()) {
            redirect('/');
        }
    }
    public static function requireAuth()
    {
        if (self::guest()) {
            Session::flash('errors', ['Please log in to access this page.']);
            redirect('/login');
        }
    }
}
