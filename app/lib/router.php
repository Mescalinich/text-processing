<?php

namespace App\Lib;

class Router {

    protected $routes;
    protected $renderer;

    function __construct($routesPath){
        // Получаем конфигурацию из файла.
        $this->routes = include($routesPath);
        $this->renderer = new Templater(APP_VIEWS_PATH);
    }

    function getURI(){
        if(!empty($_SERVER['REQUEST_URI'])) {
            return trim($_SERVER['REQUEST_URI'], '/');
        }
        return '/';
    }

    function run(){
        $uri = $this->getURI();

        foreach($this->routes as $pattern => $route){
            if($pattern === $uri){
                $internalRoute = preg_replace("~$pattern~", $route, $uri);
                $segments = explode('/', $internalRoute);
                $controller = 'App\\Controllers\\' . ucfirst(array_shift($segments)).'_Controller';
                $action = strtolower(array_shift($segments)) . 'Action';
                $parameters = $segments;

                if(!is_callable(array($controller, $action))){

                    header("HTTP/1.0 404 Not Found");
                    echo $this->renderer->render('404.html');
                    return;
                }

                $ctrl = new $controller($this->renderer);
                call_user_func_array(array($ctrl, $action), $parameters);
                return;
            }
        }

        header("HTTP/1.0 404 Not Found");
        echo $this->renderer->render('404.html');
        return;
    }
}
