<?php

namespace controllers;

/**
 * Description of IndexController
 *
 * @author Marc Remolt <m.remolt@webmasters.de>
 */
class IndexController
{
    public function executeIndex()
    {
        
    }

    public function run($action, $params = array())
    {
        var_dump($params);
    }
}
