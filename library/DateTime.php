<?php

namespace library;
use \DateTime as PHPDateTime;

class DateTime extends PHPDateTime
{
    public function __toString()
    {
        return $this->format('Y-m-d');
    }
}
