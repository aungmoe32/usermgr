<?php

namespace Core;

class CsrfTokenException extends \Exception
{
    protected $message = 'Invalid CSRF token';
    protected $code = 403;
}
