<?php

namespace is\Masters\Extenders\Reactive;

use is\Helpers\System;
use is\Helpers\Strings;
use is\Helpers\Objects;
use is\Helpers\Parser;
use is\Helpers\Prepare;
use is\Helpers\Paths;
use is\Helpers\Local;
use is\Components\Language;
use is\Masters\View;

class Reactive
{
    public $folder;
    public $time;
    public $string;

    public function __construct()
    {
        //$lang = Language::getInstance();
        //$this->setData($lang->getData());
    }

    public function launch($folder, $time = 5)
    {
        $this->folder = $folder;
        $this->time = $time;

        $this->setList();
        //echo $this->getList();
    }

    public function setList()
    {
        $list = Local::list($this->folder, ['subfolders' => true, 'return' => 'files', 'merge' => true]);

        Objects::each($list, function ($item) {
            $this->string .= 'x' . Strings::get(filemtime($item['fullpath']), -4);
        });
    }

    public function getList()
    {
        return $this->string;
    }
}
