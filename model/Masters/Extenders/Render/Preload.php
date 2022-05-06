<?php

namespace is\Masters\Extenders\Render;

use is\Helpers\System;
use is\Helpers\Objects;
use is\Helpers\Strings;
use is\Helpers\Local;
use is\Helpers\Paths;
use is\Helpers\Parser;

class Preload extends Master
{
    public function launch($name)
    {
        $item = Strings::pairs($name);
        $i = Strings::pairs($item[1]);

        $as = $item[0];
        $type = $i[0];
        $link = $i[1];

        return
            '<link
                rel="preload"
                href="' . Paths::prepareUrl($link) . '"
                as="' . $as . '"
                ' . ($type ? 'type="' . $type . '" ' : null) . '
                crossorigin="anonymous"
            >';
    }
}
