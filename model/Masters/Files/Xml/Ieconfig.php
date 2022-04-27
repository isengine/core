<?php

namespace is\Masters\Files\Xml;

use is\Helpers\System;
use is\Helpers\Strings;
use is\Helpers\Objects;
use is\Helpers\Parser;
use is\Helpers\Prepare;
use is\Helpers\Paths;
use is\Helpers\Sessions;
use is\Helpers\Local;
use is\Parents\Data;
use is\Components\Router;
use is\Components\State;
use is\Components\Uri;
use is\Components\Config;
use is\Masters\View;
use is\Masters\Files\Master;

class Ieconfig extends Master
{
    public function launch()
    {
        $view = View::getInstance();

        $webapp = $view->get('state|settings:webapp');
        $icons = $view->get('icon|data');
        $path = $view->get('state|domain') . $icons['settings']['path'] . '/';

        Sessions::setHeader(['Content-type' => 'application/xml; charset=utf-8']);

        echo '<?xml version="1.0" encoding="utf-8"?>';
        ?>
<browserconfig>
    <msapplication>
        <tile>
        <?php
        foreach ($icons['msapplication']['sizes'] as $item) {
            $size = Parser::fromString($item);
            if (empty($size[1])) {
                $size[1] = $size[0];
            }
            $item = $size[0] . 'x' . $size[1];
            $type = $size[1] === $size[0] ? 'square' : 'wide';
            $ipath = $path . $icons['msapplication']['name'] . '-' . $item . '.png';
            ?>
            <<?= $item === '144x144' ? 'TileImage' : $type . $item . 'logo'; ?> src="<?= $ipath; ?>" />
            <?php
            unset($ipath, $type, $size);
        }
        unset($item);
        ?>
            <TileColor><?= $webapp['color']; ?></TileColor>
        </tile>
        <?php /*
        <notification>
            <polling-uri src="/notifications/contoso1.xml"/> // ссылка на новости или статьи, разрешено до 5 элементов polling-uri2 ... polling-uri5
            <frequency>30</frequency> // обновление в минутах: 30, 60, 360, 720 или 1440 (в часах это 0.5, 1, 6, 12 и 24)
            <cycle>1</cycle>
        </notification>
        */?>
    </msapplication>
</browserconfig>
        <?php
        unset($webapp, $icons, $path);
    }
}
?>