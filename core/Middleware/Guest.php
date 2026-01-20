<?php

namespace Core\Middleware;

use Core\Authenticator;

class Guest
{
    public function handle()
    {
        Authenticator::onlyGuest();
    }
}
