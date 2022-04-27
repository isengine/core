<?php

namespace is\Masters\Files\Js;

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
use is\Masters\View;
use is\Masters\Files\Master;

class Serviceworker extends Master
{
    // https://habr.com/ru/post/358060/

    public function launch()
    {
        $view = View::getInstance();
        $uri = Uri::getInstance();

        $webapp = $view->get('state|settings:webapp');
        $icons = $view->get('icon|data');

        $path = DI . (
            !empty($webapp['serviceworker']['path']) ? str_replace(
                ':',
                DS,
                $webapp['serviceworker']['path']
            ) . DS : null
        ) . $webapp['serviceworker']['name'];
        $sw = null;
        if (!empty($webapp['serviceworker']['name'])) {
            if (file_exists($path)) {
                $sw = Local::readFile($path);
            } elseif (!empty($webapp['serviceworker']['link'])) {
                Local::saveFromUrl($path, $webapp['serviceworker']['link']);
                $sw = Local::readFile($path);
            }
        }

        if ($uri->path['string'] === 'serviceworker.js') {
            Sessions::setHeader(['Content-type' => 'application/javascript; charset=utf-8']);
            echo $sw;
        } else {
            Sessions::setHeaderCode(404);
        }

        // read and echo content another file (as js) which set in template->webapp
        // and print push-messages
    }
}
