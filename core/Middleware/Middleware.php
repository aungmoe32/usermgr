<?php

namespace Core\Middleware;

class Middleware
{
    public const MAP = [
        'authenticated' => Authenticated::class,
        'guest' => Guest::class
    ];

    public const GLOBAL = [
        'csrf' => CsrfMiddleware::class
    ];


    public static function resolve($key)
    {
        if (!$key) {
            return;
        }

        // Check both MAP and GLOBAL arrays for middleware
        $middleware = static::MAP[$key] ?? static::GLOBAL[$key] ?? false;

        if (!$middleware) {
            throw new \Exception("No matching middleware found for key '{$key}'.");
        }

        (new $middleware)->handle();
    }

    public static function applyGlobal()
    {
        foreach (static::GLOBAL as $key => $middleware) {
            static::resolve($key);
        }
    }
}
