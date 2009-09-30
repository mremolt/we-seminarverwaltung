<?php

$routesMapper = new Horde_Routes_Mapper();

// hier werden alle Routen definiert

// Die Standardrouten
$routesMapper->connect('/', array('controller' => 'index', 'action' => 'index'));
$routesMapper->connect(':controller/:action/:id');

$routesMapper->createRegs(library\BaseController::getValidControllerUrls());
