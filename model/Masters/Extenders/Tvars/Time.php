<?php

namespace is\Masters\Extenders\Tvars;

use is\Helpers\Strings;
use is\Components\Datetime;

class Time extends Master
{
    public function launch($data = null)
    {
        $time = Datetime::getInstance();
        return $time->get(Strings::join($data, ':'));
    }
}
