<?php

namespace is\Masters\Extenders\Tvars;

use is\Helpers\Prepare;

class Mail extends Master
{
    public function launch($data)
    {
        $url = $data[0];
        $class = $data[1] ? ' class="' . $data[1] . '"' : null;

        if (empty($data[2])) {
            $data[2] = $url;
        }

        $subject = !empty($data[3]) ? '?subject=' . $data[3] : null;

        return '<a href="mailto:' . $url . $subject . '" alt="' . Prepare::tags($data[2]) . '"' . $class . '>' . $data[2] . '</a>';
    }
}
