<?php

namespace controllers;
use library\BaseController, library\Auth;

/**
 * Description of AuthController
 *
 * @author Marc Remolt <m.remolt@webmasters.de>
 * @package controllers
 */
class AuthController extends BaseController
{
    public function loginAction()
    {
        if ($this->isPost()) {
            Auth::logout();
            Auth::authenticate($_POST['email'], $_POST['passwort']);
            if (Auth::isLoggedIn()) {
                $this->redirectTo();
            } else {
                $this->addMessage('Logindaten waren leider fehlerhaft.');
            }
        }
    }

    public function logoutAction()
    {
        Auth::logout();
    }
}