<?php

namespace Core;

class Csrf
{
    public static function generateToken(): string
    {
        if (!Session::has('csrf_token')) {
            $token = bin2hex(random_bytes(32));
            Session::put('csrf_token', $token);
        }

        return Session::get('csrf_token');
    }

    public static function validateToken(?string $token): bool
    {
        if (!$token || !Session::has('csrf_token')) {
            return false;
        }

        return hash_equals(Session::get('csrf_token'), $token);
    }
}
