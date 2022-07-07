<?php

namespace is\Masters\Extenders\Tvars;

use is\Helpers\Objects;
use is\Helpers\Prepare;

class Email extends Master
{
    public function launch($data)
    {
        $data = Objects::createByIndex(
            [0, 1, 2, 3],
            $data
        );

        $url = $data[0];
        $class = $data[1] ? ' class="' . $data[1] . '"' : '';

        if (!$data[2]) {
            $data[2] = $url;
        }

        $subject = $data[3] ? '?subject=' . $data[3] : '';

        return '<a href="mailto:' . $url . $subject . '"'
            . ' alt="' . Prepare::tags($data[2]) . '"'
            . $class
            . '>'
            . $data[2]
            . '</a>';
    }
}
