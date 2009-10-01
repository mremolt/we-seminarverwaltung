<?php

require_once 'config/config.php';
require_once 'library/autoload.php';
require_once 'config/routes.php';

$route = library\Router::getInstance()->match(RELATIVE_URL);

$controllerFile = 'controllers' . DIRECTORY_SEPARATOR . ucfirst($route['controller']) . 'Controller.php';
if ( file_exists($controllerFile) ) {
    // falls zu der Route ein Controller exisitert
    $controllerName = 'controllers\\' . ucfirst($route['controller']) . 'Controller';
    $controller = new $controllerName();
    $controller->run($route['action'], $route);
} else {
    // ansonsten leite auf die 404 Seite weiter
    $controller = new \controllers\ErrorController();
    $controller->run('notfound');
}
