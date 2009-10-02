<?php

namespace library;
use \DateTime as PHPDateTime;

/**
 * Erweiterung der in PHP eingebauten DateTime-Klasse
 *
 * @author Marc Remolt <m.remolt@webmasters.de>
 * @package library
 */
class DateTime extends PHPDateTime
{
    public function __toString()
    {
        return $this->format('Y-m-d');
    }
}
