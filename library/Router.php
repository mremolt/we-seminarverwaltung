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

    /**
     * Getter
     *
     * @return array
     */
    public function getRoutes()
    {
        return $this->routes;
    }

    /**
     * Fügt dem Router eine neue Route hinzu
     *
     * @param string $url
     * @param string $controller
     * @param string $action
     * @return Router
     */
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

    /**
     * Gibt die Route passend zu der übergebenen URL zurück
     *
     * @param string $url
     * @return array
     */
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

            // hat eine URL keinen Controller oder Action, verwende index als Standard
            $controller = array_key_exists(0, $parts) ? $parts[0] : 'index';
            $action     = array_key_exists(1, $parts) ? $parts[1] : 'index';

            // falls die URL '/' übergeben wurde, ist in $controller nun ein leerer String 
            // FIXME: bessere Lösung für Workaround
            if ( empty($controller) ) {
                $controller = 'index';
            }

            $route = array (
                'controller' => $controller,
                'action'     => $action,
            );
        }
        return $route;
    }

    /**
     * Liefert die URL zu einem Controller/Action-Paar
     *
     * @param string $controller
     * @param string $action
     * @return $string
     */
    public function getUrlFor($controller = 'index', $action = 'index')
    {
        $route = array('controller' => $controller, 'action' => $action);
        $routes = $this->getRoutes();
        
        // suche erst nach einer manuell festgelegten Route
        $url = array_search($route, $routes);
        if ($url === false) {
            // wenn keine gefunden wurde
            $url = $controller . '/' . $action;
        }

        // hänge die PROJECT_URL als Prefix an die URL
        $url = PROJECT_URL . '/' . $url;
        return $url;
    }
}