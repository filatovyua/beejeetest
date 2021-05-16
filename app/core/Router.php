<?php

namespace beejeetest\app\core;

use Symfony\Component\HttpFoundation\Request;
use beejeetest\app\controllers\ControllerErrorPage404;

class Router {

    public static function Start() {

        $request = Request::createFromGlobals();
        $path = explode('/', ltrim($request->getPathInfo(), '/'));
        $controllerName = "\\beejeetest\\app\\controllers\\Controller" . (ucfirst(strtolower($path[0] ?: "index")));
        $actionName = strtolower($path[1] ?? "index");
        if (class_exists($controllerName) && method_exists($controllerName, $actionName)) {
            $controller = new $controllerName();
            $response = $controller->$actionName($request);
            echo $response->getContent();
        } else {
            Router::ErrorPage404();
        }
    }

    public static function ErrorPage404() {
        $controller = new ControllerErrorPage404();
        $controller->index();
    }

}
