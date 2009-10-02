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
        $order_by = array_key_exists('order_by', $_GET) ? $_GET['order_by'] : false;
        $asc      = (array_key_exists('asc', $_GET) && $_GET['asc'] === 'false') ? false : true;
        $page     = array_key_exists('page', $_GET) ? intval($_GET['page']) : 1;

        $limit = ELEMENTS_PER_PAGE;
        $offset = (ELEMENTS_PER_PAGE * $page) - ELEMENTS_PER_PAGE;

        $seminare = Seminar::findAll($order_by, $asc, $limit, $offset);


        $paging_url  = $this->urlFor('seminar', 'index') . '?';
        $paging_url .= $order_by ? 'order_by=' . $order_by . '&' : '';
        $paging_url .= $asc ? 'asc=true&' : 'asc=false&';
        $paging_url .= 'page=';

        $this->setContext('seminare', $seminare);
        $this->setContext('title', 'Liste aller Seminare');
        $this->setContext('asc', $asc);
        $this->setContext('page', $page);
        $this->setContext('paging_url', $paging_url);

        $this->setContext('number_of_pages', ceil(Seminar::count() / ELEMENTS_PER_PAGE));
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
