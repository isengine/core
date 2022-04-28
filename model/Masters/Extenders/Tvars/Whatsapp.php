<?php

namespace is\Masters\Extenders\Tvars;

use is\Helpers\Strings;
use is\Helpers\Prepare;
use is\Helpers\Objects;
use is\Components\Language;

class Whatsapp extends Master
{
    public function launch($data)
    {
        $data = Objects::createByIndex(
            [0, 1, 2, 3],
            $data
        );

        $phone = $data[0];
        $class = $data[1] ? ' class="' . $data[1] . '"' : null;

        if ($data[3]) {
            $data[3] = '?text=' . Prepare::urlencode(Prepare::tags($data[3]));
        }

        $lang = Language::getInstance();
        $phone = Strings::unfirst(Prepare::phone($phone, $lang->get('lang')));

        return '<a href="https://wa.me/' . $phone . $data[3] . '" alt="' . Prepare::tags($data[2]) . '"' . $class . ' target="blank">' . $data[2] . '</a>';
    }
}
