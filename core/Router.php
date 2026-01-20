<?php

namespace Core;

use Core\Middleware\Authenticated;
use Core\Middleware\Guest;
use Core\Middleware\Middleware;
use Core\Middleware\CsrfMiddleware;

class Router
{
    protected $routes = [];

    public function add($method, $uri, $controller, $middlewares = null)
    {
        $this->routes[] = [
            'uri' => $uri,
            'controller' => $controller,
            'method' => $method,
            'middlewares' => $middlewares ?? []
        ];

        return $this;
    }

    public function get($uri, $controller, $middlewares = null)
    {
        return $this->add('GET', $uri, $controller, $middlewares);
    }

    public function post($uri, $controller, $middlewares = null)
    {
        return $this->add('POST', $uri, $controller, $middlewares);
    }

    public function delete($uri, $controller, $middlewares = null)
    {
        return $this->add('DELETE', $uri, $controller, $middlewares);
    }

    public function patch($uri, $controller, $middlewares = null)
    {
        return $this->add('PATCH', $uri, $controller, $middlewares);
    }

    public function put($uri, $controller, $middlewares = null)
    {
        return $this->add('PUT', $uri, $controller, $middlewares);
    }

    public function only($key)
    {
        $this->routes[array_key_last($this->routes)]['middleware'] = $key;

        return $this;
    }

    public function route($uri, $method)
    {
        foreach ($this->routes as $route) {
            if ($route['uri'] === $uri && $route['method'] === strtoupper($method)) {
                Middleware::applyGlobal();

                // Handle route-specific middlewares if they exist
                foreach ($route['middlewares'] as $middleware) {
                    Middleware::resolve($middleware);
                }

                return require base_path('http/controllers/' . $route['controller']);
            }
        }

        abort();
    }
}
