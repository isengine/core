<?php

namespace is\Masters\Extenders\Tvars;

use is\Helpers\Prepare;
use is\Components\Language;
use is\Helpers\Objects;

class Phone extends Master
{
    public function launch($data)
    {
        $data = Objects::createByIndex(
            [0, 1, 2],
            $data
        );

        $url = $data[0];
        $class = $data[1] ? ' class="' . $data[1] . '"' : null;

        if (!$data[2]) {
            $data[2] = $url;
        }

        $lang = Language::getInstance();
        $url = Prepare::phone($url, $lang->get('lang'));

        return '<a href="tel:' . $url . '" alt="' . Prepare::tags($data[2]) . '"' . $class . '>' . $data[2] . '</a>';
    }
}
