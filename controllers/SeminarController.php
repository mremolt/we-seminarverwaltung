<?php

namespace controllers;
use library\BaseController, models\Seminar, library\Auth;

/**
 * Description of SeminarController
 *
 * @author Marc Remolt <m.remolt@webmasters.de>
 * @package controllers
 */
class SeminarController extends BaseController
{
    public function _preRun()
    {
        Auth::requireLogin();
    }

    public function indexAction()
    {
        $paging_data = $this->_getPagingData('Seminar', $this->urlFor('seminar', 'index'));
        extract($paging_data);

        $seminare = Seminar::findAll($order_by, $asc, $limit, $offset);

        $this->setContext('seminare', $seminare);
        $this->setContext('title', 'Liste aller Seminare');
    }

    public function neuAction()
    {
        if ($this->isPost()) {
            $seminar = new Seminar($_POST);
            if ($seminar->isValid()) {
                $seminar->save();
                $this->addMessage('Das Seminar wurde erfolgreich gespeichert');
                $this->redirectTo('seminar', 'index');
            }
        } else {
            $seminar = new Seminar();
        }
        $this->setContext('seminar', $seminar);
        $this->setContext('save_url', $this->urlFor('seminar', 'neu'));
    }

    public function editierenAction()
    {
        $seminar = Seminar::find($_REQUEST['id']);
        if ($this->isPost()) {
            $seminar->fromArray($_POST);

            if ($seminar->isValid()) {
                $seminar->save();
                $this->addMessage('Das Seminar wurde erfolgreich gespeichert');
                $this->redirectTo('seminar', 'index');
            }
        }
        
        $this->_templateName = 'neu';
        $this->setContext('title', 'Seminar "' . $seminar . '" editieren');
        $this->setContext('seminar', $seminar);
        $this->setContext('save_url', $this->urlFor('seminar', 'editieren'));
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
