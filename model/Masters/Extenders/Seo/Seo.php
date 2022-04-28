<?php

namespace is\Masters\Extenders\Seo;

use is\Helpers\System;
use is\Helpers\Strings;
use is\Helpers\Objects;
use is\Helpers\Prepare;
use is\Parents\Data;
use is\Masters\Database;
use is\Components\Router;
use is\Masters\Extenders\Tvars\Tvars;
use is\Masters\View;

class Seo extends Tvars
{
    public function __construct()
    {
        // запуск родительского конструктора

        parent::__construct();

        // инициализация настроек

        $router = Router::getInstance();

        $db = Database::getInstance();

        $db->collection('seo');
        $db->driver->filter->addFilter('name', '+' . $router->template['name']);
        $db->driver->filter->addFilter('type', 'settings');
        $db->launch();

        $this->setData($db->data->getFirstData());

        $db->clear();

        // старое условие, где очень странный принцип выбора страницы
        //$page = System::typeClass($router->current, 'entry') ? $router->current->getEntryData('name') : 'index';
        //if ($page) {
        //    $db->driver->filter->addFilter('name', '+' . $page);
        //    $db->launch();
        //    $this->mergeData( $db->data->getFirstData() );
        //    $db->clear();
        //}

        // новое условие, более корректное
        // хотя, на мой взгляд, роутер должен определять имя index самостоятельно,
        // это должно быть зашито прямо внутри класса
        // он не берет информацию из контента
        // и это нужно реализовать
        // либо я чего-то не понимаю
        if (System::typeClass($router->current, 'entry')) {
            $page = $router->current->getEntryData('name');
        }
        if (!$page) {
            $page = 'index';
        }
        $db->driver->filter->addFilter('name', '+' . $page);
        $db->launch();
        $this->mergeData($db->data->getFirstData());

        $db->clear();

        if (
            $this->getData('content') &&
            $router->content['name']
        ) {
            $this->launchByContent();
        }

        $this->launchByData();
    }

    public function title()
    {
        $this->addData(
            'fulltitle',
            $this->getData('pre') . $this->getData('title') . $this->getData('post')
        );
    }

    public function keys()
    {
        $keys = $this->getData('keywords');

        if (!System::typeIterable($keys)) {
            $keys = Strings::split($keys, ',');
        }

        if (!System::typeIterable($keys)) {
            $data = Strings::split($this->getData('description'));
            if (System::typeIterable($data)) {
                $c = 0;
                $limit = 200;
                foreach ($data as $item) {
                    $item = Prepare::clear($item);
                    $item = Prepare::words($item);
                    $len = Strings::len($item);
                    if ($len > 4) {
                        $keys[] = $item;
                    }
                    $c += $len;
                    if ($c >= $limit) {
                        break;
                    }
                }
                unset($item);
            }
        }

        $this->addData('tags', $keys);
        $this->addData('keywords', Strings::join($keys, ', '));
    }

    public function launchByContent()
    {
        if (!$this->getData('content')) {
            return;
        }

        $view = View::getInstance();

        $content = $view->get('content')->getData();

        foreach ($this->getData('content') as $key => $item) {
            $this->addData(
                $key,
                $content[$item]
            );
        }
        unset($key, $item);
    }
}
