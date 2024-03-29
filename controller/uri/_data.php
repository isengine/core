<?php

namespace is;

use is\Helpers\System;
use is\Helpers\Strings;
use is\Helpers\Objects;
use is\Components\Uri;
use is\Components\Config;

// читаем uri

$config = Config::getInstance();
$uri = Uri::getInstance();

// устанавливаем настройки rest
// запрещен, разрешен и если разрешен, то с ключами или без

if ($config->get('url:rest')) {
    //$uri->rest = $config->get('url:rest');
    //$uri->keys = $config->get('url:keys');
    $uri->setRest(
        $config->get('url:rest'),
        $config->get('url:keys')
    );
}

// ini

if ($uri->file['extension'] === 'ini') {
    $state->set('error', 404);
}

System::debug($uri);
exit;

// разбираем данные
/*
$data = [];
$array = null;
$path_array = $uri->getPathArray();

if ( System::type($config->get('url:rest'), 'numeric') ) {
    $array = Objects::get($path_array, $config->get('url:rest') - 1);
} else {
    $find = Objects::find($path_array, $config->get('url:rest'));
    if (System::set($find)) {
        $array = Objects::get($path_array, $find + 1);
    }
}

if ($array) {
    if ($config->get('url:keys')) {
        $data = Objects::split($array);
    } else {
        $data = Objects::reset($array);
    }
}

if ($config->get('url:query')) {
    $data = Objects::merge($data, $uri->query['array']);
    if (System::server('method') === 'post') {
        $data = Objects::merge($data, $_POST);
    }
}

$uri->setData($data);

unset($data);
*/

//echo print_r($data, 1);
//echo '[' . print_r($match, 1) . ']';
//echo '[' . print_r($find, 1) . ']';

//echo '<pre>';
//echo print_r($uri, 1);
//exit;

// здесь теперь куча новых настроек, однако на данный момент
// роутинг в ури унесен вообще в запределье
// router/rest.php
// хотя это до сих пор относится к ури, роутер из ури потом парсит
// надо проверить, зачем это и можно ли его перетащить поближе сюда
// нужны тесты и смотреть код
// ну, вдруг он там для того, чтобы не мешать еще каким-то условиям

// а еще это хотелось бы сделать так, чтобы можно было задавать персональный REST для разных разделов
// вот это было бы вообще вау-вау
