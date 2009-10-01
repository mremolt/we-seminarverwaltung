<?php

$router = library\Router::getInstance();

// hier werden alle Routen definiert
$router->connect('/seminare/auflisten', 'seminar', 'index');
