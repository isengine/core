<?php

namespace is\Masters\Extenders\Layout;

use is\Helpers\Sessions;
use is\Helpers\Parser;
use is\Helpers\Objects;
use is\Helpers\Strings;
use is\Helpers\Local;
use is\Helpers\System;
use is\Helpers\Matches;
use is\Helpers\Paths;

class Pages extends Master
{
    public function setCache()
    {
        return $this->paths['cache'] . $this->template . DS . 'inner' . DS . $this->name . '.php';
    }

    public function setPath()
    {
        return $this->paths['base'] . $this->template . DS . 'html' . DS . 'inner' . DS . $this->name . '.php';
    }
}
