<?php

namespace Core\Middleware;

use Core\Csrf;
use Core\CsrfTokenException;

class CsrfMiddleware
{
    public function handle()
    {
        $unsafeMethods = ['POST', 'PUT', 'PATCH', 'DELETE'];
        $requestMethod = $_SERVER['REQUEST_METHOD'];

        // Handle method override for PUT/PATCH/DELETE requests sent as POST
        if ($requestMethod === 'POST' && isset($_POST['_method'])) {
            $requestMethod = strtoupper($_POST['_method']);
        }

        if (in_array($requestMethod, $unsafeMethods)) {
            $token = $_POST['csrf_token'] ?? $_POST['_token'] ?? null;

            if (!Csrf::validateToken($token)) {
                throw new CsrfTokenException();
            }
        }
    }
}
