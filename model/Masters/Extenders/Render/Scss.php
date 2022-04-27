<?php

namespace is\Masters\Extenders\Render;

use is\Helpers\Sessions;
use is\Helpers\Parser;
use is\Helpers\Objects;
use is\Helpers\Strings;
use is\Helpers\Local;
use is\Helpers\System;
use is\Helpers\Matches;
use is\Helpers\Paths;
use ScssPhp\ScssPhp\Compiler;
use ScssPhp\ScssPhp\OutputStyle;

class Scss extends Master
{
    public function launch($name)
    {
        // name - имя файла, без расширения и без пути

        $from_folder = $this->getPathByKey('from', 'scss');
        $url_folder = $this->getPathByKey('url', 'scss');

        $this->setPathByKey('from', 'scss:' . $name . '.scss');
        $this->setPathByKey('to', 'scss:' . $name . '.css');
        $this->setPathByKey('url', 'scss:' . $name . '.css');

        $this->setHash();

        if (!$this->matchHash()) {
            $data = $this->rendering($from_folder, $name);
            if ($data) {
                $this->write($data);
                $this->writeHash();
            }
            unset($data);
        }

        return '<link rel="stylesheet" type="text/css" href="' . $this->url . $this->modificator() . '" />';
    }

    public function rendering($path, $name)
    {
        // рендеринг
        $scss = new Compiler();
        $scss->setImportPaths($path);
        $scss->setFormatter('\ScssPhp\ScssPhp\Formatter\Expanded');
        $scss->setOutputStyle(OutputStyle::COMPRESSED);
        return $scss->compile('@import "' . $name . '.scss";');
    }
}
