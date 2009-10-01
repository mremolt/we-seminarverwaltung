<?php

$router = library\Router::getInstance();

// hier werden alle Routen definiert
$router->connect('/bla/blub', 'seminar', 'index');
