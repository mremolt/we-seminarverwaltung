<?php

namespace controllers;
use library\BaseController;

/**
 * Description of IndexController
 *
 * @author Marc Remolt <m.remolt@webmasters.de>
 * @package controllers
 */
class ErrorController extends BaseController
{
    public function notfoundAction()
    {
        header("HTTP/1.0 404 Not Found");
        $this->setContext('title', '404 not found');
    }
}