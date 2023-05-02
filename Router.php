<?php

namespace Musanna\MvcCore;

use Musanna\MvcCore\Exception\NotFoundException;

class Router
{
    protected array $routes = []; 

    public function __construct(
        public readonly Request $request,
        public readonly Response $response,
    ) {
    }
    public function get(string $path, callable|string|array $callback)
    {
        $this->routes['get'][$path] = $callback;
        return $this;
    }

    public function post(string $path, callable|string|array $callback)
    {
        $this->routes['post'][$path] = $callback;
        return $this;
    }

    public function resolve()
    {
        $path    = $this->request->getPath();
        $method  = $this->request->getMethod();
        $callback= $this->routes[$method][$path] ?? false;

        if(!$callback) {
            throw new NotFoundException();
        }

        if(is_string($callback)) {
            return Application::$app->view->renderView($callback);
        }

        if(is_array($callback)) {
            $controller   = new $callback[0];
            Application::$app->controller =  $controller;
            $controller->action = $callback[1];
            $callback[0] = $controller;
            
            foreach($controller->getMiddlewares() as $middleware ) {
                $middleware->execute();
            }
            return call_user_func_array($callback,[$this->request,$this->response]);
        }
        return call_user_func($callback,$this->request,$this->response);
    }
}