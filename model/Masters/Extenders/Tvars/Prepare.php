<?php

namespace is\Masters\Extenders\Tvars;

use is\Helpers\Prepare;
use is\Helpers\Objects;

class Prepare extends Master
{
    public function launch($data)
    {
        $data = Objects::createByIndex(
            [0, 1],
            $data
        );

        $name = $data[0];
        return Prepare::$name($data[1]);
    }
}
