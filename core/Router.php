<?php
namespace Core;

class Router
{
    protected static array $routes = [];
    protected static array $groupStack = [];

    public static function get(string $path, $handler, array $middleware = [])
    {
        self::addRoute('GET', $path, $handler, $middleware);
    }

    public static function post(string $path, $handler, array $middleware = [])
    {
        self::addRoute('POST', $path, $handler, $middleware);
    }

    public static function addRoute(string $method, string $path, $handler, array $middleware = [])
    {
        $prefix = self::currentGroupPrefix();
        $mergedMiddleware = array_merge(self::currentGroupMiddleware(), $middleware);

        $fullPath = rtrim($prefix . $path, '/');

        self::$routes[] = [
            'method'     => strtoupper($method),
            'path'       => $fullPath ?: '/',
            'handler'    => $handler,
            'middleware' => $mergedMiddleware,
        ];
    }

    public static function group(string $prefix, array $options, callable $callback)
    {
        self::$groupStack[] = [
            'prefix' => rtrim($prefix, '/'),
            'middleware' => $options['middleware'] ?? [],
        ];

        $callback();

        array_pop(self::$groupStack);
    }

    protected static function currentGroupPrefix(): string
    {
        return implode('', array_column(self::$groupStack, 'prefix'));
    }

    protected static function currentGroupMiddleware(): array
    {
        return array_merge([], ...array_column(self::$groupStack, 'middleware'));
    }

    public function dispatch(string $uri, string $method)
{
    $uri = '/' . trim(parse_url($uri, PHP_URL_PATH), '/');

    foreach (self::$routes as $route) {
        if ($route['method'] !== strtoupper($method)) continue;

        // Convert route path with {param} to regex
        $pattern = preg_replace('#\{[a-zA-Z_][a-zA-Z0-9_]*\}#', '([^/]+)', $route['path']);
        $pattern = '#^' . $pattern . '$#';

        if (preg_match($pattern, $uri, $matches)) {
            array_shift($matches); // Remove full match
            $this->runMiddleware($route['middleware']);
            return $this->runHandler($route['handler'], $matches);
        }
    }

    http_response_code(404);
    echo "<h1>404 - Page not found</h1>";
}

protected function runHandler($handler, array $params = [])
{
    if (is_callable($handler)) {
        return call_user_func_array($handler, $params);
    }

    // Handle [ClassName, method] form
    if (is_array($handler) && count($handler) === 2 && is_string($handler[0])) {
        $className = $handler[0];
        $method    = $handler[1];

        if (!class_exists($className)) {
            throw new \Exception("Controller class $className does not exist.");
        }

        $controller = new $className();

        if (!method_exists($controller, $method)) {
            throw new \Exception("Method $method not found in controller $className.");
        }

        return call_user_func_array([$controller, $method], $params);
    }

    throw new \Exception('Invalid route handler.');
}

    protected function runMiddleware(array $middleware)
    {
        foreach ($middleware as $name) {
            $func = "\\Core\\Middleware\\$name";
            if (function_exists($func)) {
                $func();
            } elseif (is_callable($name)) {
                call_user_func($name);
            } else {
                throw new \Exception("Middleware not found: " . $name);
            }
        }
    }


}