<?php

namespace controllers;
use library\BaseController;
use models\Seminartermin, models\Seminar;

/**
 * Description of SeminarterminController
 *
 * @author Marc Remolt <m.remolt@webmasters.de>
 * @package controllers
 */
class SeminarterminController extends BaseController
{
    public function indexAction()
    {
        $seminartermine = Seminartermin::findAll();
        $this->setContext('seminartermine', $seminartermine);
        $this->setContext('title', 'Liste aller Seminartermine');
    }

    public function neuAction()
    {
        if ($this->isPost()) {
            $seminar_id = $_POST['seminar_id'];
            $seminartermin = new Seminartermin($_POST);
            if ($seminartermin->isValid()) {
                $seminartermin->save();
                $this->addMessage('Der Seminartermin wurde erfolgreich gespeichert');
                $this->redirectTo('seminartermin', 'index');
            }
        } else {
            $seminar_id = 0;
            $seminartermin = new Seminartermin();
        }
        $this->setContext('seminartermin', $seminartermin);
        $this->setContext('seminare', Seminar::findAll());
        $this->setContext('save_url', $this->urlFor('seminartermin', 'neu'));
        $this->setContext('seminar_id', $seminar_id);
    }

    public function editierenAction()
    {
        $seminartermin = Seminartermin::find($_REQUEST['id']);
        if ($this->isPost()) {
            $seminar_id = $_POST['seminar_id'];
            $seminartermin->fromArray($_POST);

            if ($seminartermin->isValid()) {
                $seminartermin->save();
                $this->addMessage('Der Seminartermin wurde erfolgreich gespeichert');
                $this->redirectTo('seminartermin', 'index');
            }
        } else {
            $seminar_id = $seminartermin->getSeminar()->getId();
        }
        
        $this->setContext('seminartermin', $seminartermin);
        $this->setContext('seminare', Seminar::findAll());
        $this->setContext('save_url', $this->urlFor('seminartermin', 'editieren'));
        $this->setContext('seminar_id', $seminar_id);
    }

    public function loeschenAction()
    {
        $seminar = Seminar::find($_REQUEST['id']);

        if ($this->isPost()) {
            $seminar->delete();
            $this->addMessage('Das Seminar "' . $seminar->getTitel() . '" wurde erfolgreich gelöscht');
            $this->redirectTo('seminar', 'index');
        }
        $this->setContext('seminar', $seminar);
    }
}
