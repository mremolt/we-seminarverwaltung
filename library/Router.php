<?php

namespace library;

/**
 * Diese Klasse ist für das Abbilden von URLs auf Controller und Actions verantwortlich
 *
 * Sie ist als Singleton implementiert, um die Routen nicht mehrfach
 * hinzufügen zu müssen.
 *
 * @author Marc Remolt <m.remolt@webmasters.de>
 * @package library
 */
class Router
{
    protected static $instance = null;

    protected $routes = array();

    /**
     * Singleton, daher muss der Konstruktor protected sein
     */
    protected function __construct()
    {

    }

    /**
     * Gibt das Exemplar dieser Klasse zurück
     * 
     * @return Router
     */
    public static function getInstance()
    {
        if (! static::$instance) {
            $class = get_called_class();
            static::$instance = new $class();
        }
        return static::$instance;
    }

    public function getRoutes()
    {
        return $this->routes;
    }

    public function connect($url, $controller, $action = 'index')
    {
        // entferne Leerzeichen und / um die URL, /blog/zeige/ wird also zu blog/zeige
        $url = trim($url, ' /');
        $this->routes[$url] = array(
            'controller' => $controller,
            'action'     => $action,
        );
        return $this;
    }

    public function match($url)
    {
        // entferne Leerzeichen und / um die URL, /blog/zeige/ wird also zu blog/zeige
        $url = trim($url, ' /');

        if ( array_key_exists($url, $this->routes) ) {
            // wenn eine spezielle Route eingetragen wurde, verwende diese
            $route = $this->routes[$url];
        } else {
            // ansonsten falle auf das Standardverhalten zurück
            $parts = explode('/', $url);

            // falls die URL '/' übergeben wurde, ist in $parts[0] ein leerer String.
            if (array_key_exists(0, $parts) && empty($parts[0])) {
                unset($parts[0]);
            }

            // hat eine URL keinen Controller oder Action, verwende index als Standard
            $controller = array_key_exists(0, $parts) ? $parts[0] : 'index';
            $action     = array_key_exists(1, $parts) ? $parts[1] : 'index';

            $route = array (
                'controller' => $controller,
                'action'     => $action,
            );
        }
        return $route;
    }
}