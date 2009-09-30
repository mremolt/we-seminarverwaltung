<?php

/**
 * Autoloader
 *
 * @param string $className
 * @package library
 */
function __autoload($className)
{
    $search = array('\\', '_');
    $replace = array(DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR);
    $path = str_replace($search, $replace, $className) . '.php';
    require $path;
}