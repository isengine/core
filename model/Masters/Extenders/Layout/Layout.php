<?php

namespace is\Masters\Extenders\Layout;

use is\Helpers\Sessions;
use is\Helpers\Parser;
use is\Helpers\Objects;
use is\Helpers\Strings;
use is\Helpers\Local;
use is\Helpers\System;
use is\Helpers\Matches;
use is\Helpers\Paths;
use is\Helpers\Prepare;
use is\Parents\Data;
use is\Masters\View;

class Layout extends Data
{
    // общие установки кэша

    public $blocks;
    public $pages;
    public $template;

    public function __construct()
    {
    }

    public function init($type, $path_base, $path_cache, $caching = 'skip')
    {
        $view = View::getInstance();
        $this->template = $view->get('state|template');

        $name = __NAMESPACE__ . '\\' . (Prepare::upperFirst($type));
        $this->$type = new $name();

        $this->$type->paths = [
            'base' => $path_base,
            'cache' => $path_cache
        ];

        if ($caching !== 'skip') {
            $this->$type->caching($caching);
        }
    }

    // public function launch($type, $name = null, $caching = 'skip')
    // DEPRECATED! use $view->get(\'block/page\')->launch(...)

    public function clear($type)
    {
        Local::eraseFolder($this->$type->cache);
    }

    public function reset($type)
    {
        $this->$type = null;
    }
}
