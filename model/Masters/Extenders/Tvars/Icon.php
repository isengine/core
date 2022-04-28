<?php

namespace is\Masters\Extenders\Tvars;

class Icon extends Master
{
    public function launch($data)
    {
        return !empty($data[0]) ? '<i class="' . $data[0] . '" aria-hidden="true"></i>' : null;
    }
}
