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
use is\Parents\Data;
use is\Masters\View;

abstract class Master extends Data
{
    public $name; // часть пути, запрошенная
    public $paths; // базовые установки пути до папки шаблона и папки кэша шаблона
    public $file_path; // полный путь до файла
    public $file_cache; // полный путь до кэша файла
    public $caching; // триггер кэширования
    public $template; // текущий или запрошенный шаблон

    public function __construct()
    {
        //$view = View::getInstance();
        //$data = $view->get('state')->getData();
        //$this->setData($data);
        //unset($data, $view);
    }

    // здесь установка путей и механизм кэширования

    public function getCache()
    {
        if (!$this->name) {
            return;
        }
        if (!$this->file_cache) {
            $this->file_cache = $this->setCache();
        }
        return $this->file_cache;
    }

    public function read()
    {
        $path = $this->getCache();

        if (!$path || !file_exists($path)) {
            return null;
        }

        // нужно прочесть даты изменения файлов
        // если дата изменения файла больше даты изменения кэша, то файл кэшируется заново

        ob_start(null, 0, PHP_OUTPUT_HANDLER_CLEANABLE | PHP_OUTPUT_HANDLER_REMOVABLE);
        foreach (Local::readFileGenerator($path) as $line) {
            echo $line;
            $data = ob_get_clean();
            echo $data;
            unset($line, $data);
        }
        ob_end_clean();
        unset($line, $data);

        return true;
    }

    public function write()
    {
        // есть проблемы со срабатыванием ob_start...end
        // т.к. они почему-то не учитывают require
        // и кэширование файла, если в нем есть вызовы,
        // получается не полным
        // и вообще, с кэшированием блоков прямо какая-то беда

        ob_start();
        $this->load();
        $data = ob_get_contents();
        ob_end_clean();
        echo $data;

        $path = $this->getCache();

        if (!$path || !$data) {
            return null;
        }

        Local::createFile($path);
        Local::writeFile($path, $data, 'replace');
    }

    public function load()
    {
        $this->file_path = null;
        if ($this->name) {
            $this->file_path = $this->setPath();
        }
        //echo $this->file_path . '<br>';
        if ($this->file_path && file_exists($this->file_path)) {
            require $this->file_path;
        }
    }

    // абстрактные методы

    abstract public function setCache();
    abstract public function setPath();

    // общие установки кэша

    public function caching($value = 'skip')
    {
        if ($value !== 'skip') {
            $this->caching = $value;
        }
    }

    // загрузка страниц или блоков

    public function includes($name, $template, $caching)
    {
        $this->template = $template;
        $this->name = $name;
        $cache_backup = $this->caching;
        $this->caching($caching);
        if ($this->caching) {
            if (!$this->read()) {
                $this->write();
            }
        } else {
            $this->load();
        }
        $this->caching($cache_backup);
    }
}
