<?php

namespace controllers;
use library\BaseController, library\Auth;
use models\Seminartermin, models\Seminar, models\Benutzer;

/**
 * Description of SeminarterminController
 *
 * @author Marc Remolt <m.remolt@webmasters.de>
 * @package controllers
 */
class SeminarterminController extends BaseController
{
    public function _preRun()
    {
        Auth::requireLogin();
    }

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
                $this->redirectTo(
                    'seminartermin',
                    'editieren',
                    '?id=' . $seminartermin->getId()
                );
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
                $this->redirectTo(
                    'seminartermin',
                    'editieren',
                    '?id=' . $seminartermin->getId()
                );
            }
        } else {
            $seminar_id = $seminartermin->getSeminar()->getId();
        }
        
        $this->setContext('seminartermin', $seminartermin);
        $this->setContext('seminare', Seminar::findAll());
        $this->setContext('teilnehmer', $seminartermin->getTeilnehmer());
        $this->setContext('save_url', $this->urlFor('seminartermin', 'editieren'));
        $this->setContext('seminar_id', $seminar_id);
    }

    public function loeschenAction()
    {
        $seminartermin = Seminartermin::find($_REQUEST['id']);

        if ($this->isPost()) {
            $seminartermin->delete();
            $this->addMessage('Der Seminartermin "' . $seminartermin . '" wurde erfolgreich gelöscht');
            $this->redirectTo('seminartermin', 'index');
        }
        $this->setContext('seminartermin', $seminartermin);
    }

    public function teilnehmer_hinzufuegenAction()
    {
        $seminartermin = Seminartermin::find($_REQUEST['seminartermin_id']);

        if ($this->isPost()) {
            $neueTeilnehmer = Benutzer::findByIds($_POST['teilnehmer_ids']);
            foreach ($neueTeilnehmer as $t) {
                $seminartermin->addTeilnehmer($t);
            }
            $message = sprintf(
                'Zum Seminartermin "%s" wurden die Teilnehmer <br />%s hinzugefügt.',
                $seminartermin,
                implode('<br />', $neueTeilnehmer)
            );
            $this->addMessage($message);
            $this->redirectTo(
                'seminartermin',
                'editieren',
                '?id=' . $seminartermin->getId()
            );
        } else {
            $nichtTeilnehmer = $seminartermin->getNichtTeilnehmer();
            $this->setContext('seminartermin', $seminartermin);
            $this->setContext('nichtTeilnehmer', $nichtTeilnehmer);
        }
    }

    public function teilnehmer_entfernenAction()
    {
        $seminartermin = Seminartermin::find($_REQUEST['seminartermin_id']);
        $teilnehmer = Benutzer::find($_REQUEST['teilnehmer_id']);

        if ($this->isPost()) {
            $seminartermin->removeTeilnehmer($teilnehmer);
            $message = sprintf(
                'Aus dem Seminartermin "%s" wurde der Teilnehmer "%s" entfernt.',
                $seminartermin,
                $teilnehmer
            );
            $this->addMessage($message);
            $this->redirectTo(
                'seminartermin',
                'editieren',
                '?id=' . $seminartermin->getId()
            );
        } else {
            $this->setContext('seminartermin', $seminartermin);
            $this->setContext('teilnehmer', $teilnehmer);
        }
    }

}
