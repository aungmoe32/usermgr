<?php

use Core\Authenticator;
use Core\Session;

Authenticator::logout();

Session::flash('success', 'You have been successfully logged out.');

redirect('/login');
