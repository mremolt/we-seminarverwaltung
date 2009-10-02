<?php

namespace controllers;
use library\BaseController, library\Auth;

/**
 * Description of IndexController
 *
 * @author Marc Remolt <m.remolt@webmasters.de>
 * @package controllers
 */
class IndexController extends BaseController
{
    public function indexAction()
    {
        $this->setcontext('title', 'Startseite');
    }

    public function infoAction()
    {
        Auth::requireLogin();
    }
}
