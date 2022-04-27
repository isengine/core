<?php

namespace is\Masters\Files\Js;

use is\Helpers\System;
use is\Helpers\Strings;
use is\Helpers\Objects;
use is\Helpers\Paths;
use is\Helpers\Sessions;
use is\Components\Config;
use is\Components\Uri;
use is\Masters\View;
use is\Masters\Files\Master;

class Isjs extends Master
{
    public function launch()
    {
        $config = Config::getInstance();
        $uri = Uri::getInstance();

        $file = DR . 'vendor' . DS . 'isengine' . DS . 'isjs' . DS . 'init.php';
        if (file_exists($file)) {
            if (!defined('ISOPTIONS')) {
                define('ISOPTIONS', json_encode([
                    'path' => Paths::toUrl(Strings::get($config->get('path:assets'), Strings::len(DI)))
                ]));
            }
            require_once $file;
        }

        $print = DI . Paths::toReal($uri->path['string']);
        if (file_exists($print)) {
            Sessions::setHeader(['Content-type' => 'application/javascript; charset=utf-8']);
            echo file_get_contents($print);
        } else {
            Sessions::setHeaderCode(404);
        }
    }
}
