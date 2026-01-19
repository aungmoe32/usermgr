<?php

use Core\Response;

function dd($value)
{
    echo "<pre>";
    print_r($value);
    echo "<br><strong>Dumped Data:</strong><br>";
    var_dump($value);
    echo "</pre>";

    die();
}

function print_it($value)
{
    echo "<pre>";
    var_dump($value);
    echo "</pre>";
}

function urlIs($value)
{
    return $_SERVER['REQUEST_URI'] === $value;
}

function abort($code = 404)
{
    http_response_code($code);

    require base_path("views/{$code}.php");

    die();
}

function authorize($condition, $status = Response::FORBIDDEN)
{
    if (! $condition) {
        abort($status);
    }

    return true;
}

function base_path($path)
{
    return BASE_PATH . $path;
}

function view($path, $attributes = [])
{
    extract($attributes);
    require base_path('views/' . $path);
}

function redirect($path)
{
    header("location: {$path}");
    exit();
}

function old($key, $default = '')
{
    return Core\Session::get('old')[$key] ?? $default;
}

function csrf_field()
{
    return sprintf(
        '<input type="hidden" name="_token" value="%s">',
        Core\Csrf::generateToken()
    );
}

function previousUrl()
{
    return $_SERVER['HTTP_REFERER'];
}

function db(): Core\Database
{
    static $database = null;

    if ($database === null) {
        $database = new Core\Database();
    }

    return $database;
}
