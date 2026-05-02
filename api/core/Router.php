<?php

require_once 'Response.php';

class Router
{
    private $routes = [];
    private $basePath = '/overgrace/api'; // 👈 ajuste aqui

    public function add($method, $route, $action)
    {
        $this->routes[] = compact('method', 'route', 'action');
    }

    public function run()
    {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        // remove base path
        if (strpos($uri, $this->basePath) === 0) {
            $uri = substr($uri, strlen($this->basePath));
        }

        if ($uri === '') $uri = '/';

        $method = $_SERVER['REQUEST_METHOD'];

        foreach ($this->routes as $r) {
            $pattern = preg_replace('/\{[a-zA-Z_]+\}/', '([^\/]+)', $r['route']);
            $pattern = "#^" . $pattern . "$#";


            if ($r['method'] === $method && preg_match($pattern, $uri, $matches)) {
                array_shift($matches);

                [$ctrl, $func] = explode('@', $r['action']);
                require_once "api/modules/$ctrl.php";

                $class = basename($ctrl);
                $obj = new $class();

                return call_user_func_array([$obj, $func], $matches);
            }
        }

        Response::json(['error' => 'Rota não encontrada'], 404);
    }
}
