<?php

namespace Core\Middleware;

use Core\Authenticator;

class Authenticated
{
    public function handle()
    {
        Authenticator::requireAuth();
    }
}
