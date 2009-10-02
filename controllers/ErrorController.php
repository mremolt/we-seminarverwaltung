<?php

namespace controllers;
use library\BaseController;

/**
 * Description of ErrorController
 *
 * @author Marc Remolt <m.remolt@webmasters.de>
 * @package controllers
 */
class ErrorController extends BaseController
{
    public function notfoundAction()
    {
        header("HTTP/1.0 404 Not Found");
        $this->setContext('title', '404 Not Found');
    }

    public function forbiddenAction()
    {
        header("HTTP/1.0 403 forbidden");
        $this->setContext('title', '403 Forbidden');
    }
}