<?php

namespace controllers;
use library\BaseController, models\Benutzer;

/**
 * Description of BenutzerController
 *
 * @author Marc Remolt <m.remolt@webmasters.de>
 * @package controllers
 */
class BenutzerController extends BaseController
{
    public function indexAction()
    {
        $this->setcontext('benutzer', Benutzer::findAll());
    }

    public function neuAction()
    {
        if ($this->isPost()) {
            $benutzer = new Benutzer($_POST);
            if ($benutzer->isValid()) {
                $benutzer->save();
                $this->addMessage('Der Benutzer wurde erfolgreich gespeichert');
                $this->redirectTo('benutzer', 'index');
            }
        } else {
            $benutzer = new Benutzer();
        }
        $this->setContext('benutzer', $benutzer);
        $this->setContext('save_url', $this->urlFor('benutzer', 'neu'));
    }

    public function editierenAction()
    {
        $benutzer = Benutzer::find($_REQUEST['id']);
        if ($this->isPost()) {
            $benutzer->fromArray($_POST);
            
            if ($benutzer->isValid()) {
                $benutzer->save();
                $this->addMessage('Der Benutzer wurde erfolgreich gespeichert');
                $this->redirectTo('benutzer', 'index');
            }
        }

        $this->_templateName = 'neu';
        $this->setContext('benutzer', $benutzer);
        $this->setContext('save_url', $this->urlFor('benutzer', 'editieren'));
    }

    public function loeschenAction()
    {
        $benutzer = Benutzer::find($_REQUEST['id']);

        if ($this->isPost()) {
            $benutzer->delete();
            $this->addMessage('Der Benutzer "' . $benutzer . '" wurde erfolgreich gelöscht');
            $this->redirectTo('benutzer', 'index');
        }
        $this->setContext('benutzer', $benutzer);
    }
}
