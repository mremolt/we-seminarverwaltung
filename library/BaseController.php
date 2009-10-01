<?php

namespace library;

/**
 * Diese Klasse bildet die Basis für alle anderen Controller
 * 
 * @author Marc Remolt <m.remolt@webmasters.de>
 * @package library
 */
abstract class BaseController
{
    protected $_params       = array();
    protected $_layoutName   = 'layout';
    protected $_templateName = '';
    protected $_context      = array();

    public function getParams()
    {
        return $this->_params;
    }

    public function getParam($name)
    {
        return $this->_params[$name];
    }

    public function setParams($params)
    {
        $this->_params = $params;
        return $this;
    }

    public function getContext()
    {
        return $this->_context;
    }

    protected function setContext($key, $value)
    {
        $this->_context[$key] = $value;
    }

    public function getTemplatePath()
    {
        return PROJECT_DIRECTORY . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR .
            $this->_getControllerShortName() . DIRECTORY_SEPARATOR .
            $this->_templateName . '.phtml';
    }

    public function run($action, $params = array())
    {
        $this->_preRun();

        $this->setParams($params);
        $this->_templateName = $action;
        
        // Standardtitel setzen
        $this->setContext('title', $action);

        // Action ausführen
        $methodName = $action . 'Action';
        if (method_exists($this, $methodName)) {
            $this->$methodName();
            extract($this->_context);

            require_once PROJECT_DIRECTORY . DIRECTORY_SEPARATOR . 'views' .
                DIRECTORY_SEPARATOR . 'layouts' . DIRECTORY_SEPARATOR .
                $this->_layoutName . '.phtml';

            // Post-Hook nur ausführen, wenn auch dieser Action ausgeführt wurde
            $this->_postRun();
        } else {
            $controller = new \controllers\ErrorController();
            $controller->run('notfound', $this->getParams());
        }
    }

    protected function _getControllerShortName()
    {
        $search = array('controllers\\', 'controller');
        $replace = array('', '');
        return str_replace($search, $replace, strtolower(get_class($this)));
    }

    /**
     * Hook-Methode
     */
    protected function _preRun()
    {

    }

    /**
     * Hook-Methode
     */
    protected function _postRun()
    {

    }
}