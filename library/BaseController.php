<?php

namespace library;

abstract class BaseController
{

    public static function getControllerFiles()
    {
        return glob(PROJECT_DIRECTORY . DIRECTORY_SEPARATOR .
            'controllers' . DIRECTORY_SEPARATOR . '*Controller.php');
    }

    public static function getValidControllerUrls()
    {
        $controllerUrls = array();
        foreach (static::getControllerFiles() as $controllerFile) {
            $controllerUrls[] = str_replace('controller.php', '', strtolower(basename($controllerFile)));
        }
        return $controllerUrls;
    }
}
