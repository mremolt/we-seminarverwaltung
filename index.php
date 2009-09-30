<?php

require_once 'config/config.php';
require_once 'library/autoload.php';
require_once 'config/routes.php';

$route = $routesMapper->match(RELATIVE_URL);
var_dump($route);

if ($route) {
    // falls eine passende Route gefunden wurde
    $controllerName = 'controllers\\' . ucfirst($route['controller']) . 'Controller';
    $controller = new $controllerName();
    $controller->run($route['action'], $route);
} else {
    // ansonsten leite auf die 404 Seite weiter
}