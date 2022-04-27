<?php

namespace is\Masters\Extenders\Render;

use is\Helpers\System;
use is\Helpers\Objects;
use is\Helpers\Strings;
use is\Helpers\Local;
use is\Helpers\Parser;
use is\Helpers\Paths;

//use \MatthiasMullie\Minify;

class Folder extends Master
{
    public function launch($name)
    {
        // name - папка, где путь разделен ':'

        $name = Parser::fromString($name, ['simple' => null]);

        if (!$name[1]) {
            $name[1] = $name[0];
        }

        if (Objects::match($name[1], '..')) {
            return;
        }

        $this->from = $this->from . Strings::join($name[0], DS) . DS;
        //$this->to = $this->to . Strings::join($name[1], DS);
        //$this->url = $this->url . Strings::join($name[1], '/');
        $this->to = DI . Strings::join($name[1], DS) . DS;
        $this->url = '/' . Strings::join($name[1], '/') . '/';

        $hash_name = Strings::replace(Strings::get($this->from, Strings::len(DR), 1, true), DS, '_');

        $this->hash = (string) filemtime($this->from);
        $hash = Local::readFile($this->to . $hash_name . '.md5');

        if (!$this->hash || !$hash || $this->hash !== $hash) {
            Local::createFolder($this->to);
            $this->rendering();
            Local::writeFile($this->to . $hash_name . '.md5', $this->hash, 'replace');
        }

        //return '<link rel="stylesheet" type="text/css" href="' . $this->url . $this->modificator() . '" />';
    }

    public function rendering()
    {
        // рендеринг
        // from - real путь, где лежит исходний файл
        // to - real путь, где будет лежать готовый файл

        $list = Local::search($this->from, ['return' => 'files', 'subfolders' => true, 'merge' => true]);

        Objects::each($list, function ($item) {
            Local::createFolder($this->to . $item['path']);
            Local::copyFile($item['fullpath'], $this->to . $item['path'] . $item['name']);
        });
    }
}
