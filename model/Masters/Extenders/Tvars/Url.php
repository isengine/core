<?php

namespace is\Masters\Extenders\Tvars;

use is\Helpers\Prepare;
use is\Helpers\Strings;
use is\Helpers\Objects;

class Url extends Master
{
    public function launch($data)
    {
        $data = Objects::createByIndex(
            [0, 1, 2],
            $data
        );

        $url = $data[0];
        $absolute = Strings::find($url, '//') === 0 ? ' target="_blank"' : null;
        $class = $data[1] ? ' class="' . $data[1] . '"' : null;

        return '<a href="' . $url . '" alt="' . Prepare::tags($data[2]) . '"' . $class . $absolute . '>' . $data[2] . '</a>';
    }
}
