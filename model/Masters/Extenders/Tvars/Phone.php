<?php

namespace is\Masters\Extenders\Tvars;

use is\Helpers\Prepare;
use is\Components\Language;

class Phone extends Master
{
    public function launch($data)
    {
        $url = $data[0];
        $class = !empty($data[1]) ? ' class="' . $data[1] . '"' : null;

        if (empty($data[2])) {
            $data[2] = $url;
        }

        $lang = Language::getInstance();
        $url = Prepare::phone($url, $lang->get('lang'));

        return '<a href="tel:' . $url . '" alt="' . Prepare::tags($data[2]) . '"' . $class . '>' . $data[2] . '</a>';
    }
}
