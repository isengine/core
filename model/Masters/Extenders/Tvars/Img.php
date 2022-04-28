<?php

namespace is\Masters\Extenders\Tvars;

use is\Helpers\Strings;
use is\Helpers\Paths;
use is\Helpers\Local;
use is\Helpers\Objects;

class Img extends Master
{
    public function launch($data)
    {
        // с помощью srcset можно организовать правильный lazyload
        // для этого нужно установить js библиотеку
        // и указать изображению соответствующий класс

        // https://apoorv.pro/lozad.js/
        // <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/lozad/dist/lozad.min.js"></script>
        // lozad('.demilazyload').observe();
        // lozad( document.querySelector('img') ).observe();

        $data = Objects::createByIndex(
            [0, 1, 2, 3],
            $data
        );

        $result = [
            Local::matchUrl($data[0], true),
            Local::matchUrl($data[1], true)
        ];

        $url = $result[0] ? $data[0] . '?' . $result[0] : $data[1] . '?' . $result[1];

        if (!$url) {
            return;
        }

        $srcset = $result[0] ? $data[1] . '?' . $result[1] : null;
        if ($srcset) {
            $srcset = ' srcset="' . $srcset . '" data-srcset="' . $url . '"';
        }

        $class = $data[2] ? ' class="' . $data[2] . '"' : null;

        return '<img src="' . $url . '"' . $srcset . ' alt="' . $data[3] . '"' . $class . ' />';
    }
}
