<?php

namespace Core\Middleware;

use Core\Csrf;
use Core\CsrfTokenException;

class CsrfMiddleware
{
    public function handle()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Csrf::validateToken($_POST['_token'] ?? null)) {
                throw new CsrfTokenException();
            }
        }
    }
}
