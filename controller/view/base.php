<?php

// Рабочее пространство имен

namespace is;

use is\Helpers\System;
use is\Helpers\Strings;
use is\Helpers\Objects;
use is\Helpers\Paths;
use is\Model\Components\Config;
use is\Model\Components\Display;
use is\Model\Components\Log;
use is\Model\Components\Router;
use is\Model\Templates\Template;

// читаем конфиг

$config = Config::getInstance();
$router = Router::getInstance();

// инициализируем шаблонизатор с параметрами

$settings = [
	'view' => $config -> get('default:view'),
	'path' => $config -> get('path:templates') . $router -> template['name'] . DS,
	'cache' => $config -> get('path:cache') . 'templates' . DS
];

$template = Template::getInstance();
$template -> init($settings);
$template -> launch();

//echo 'title::' . $template -> lang('title') . '<br>';
//echo 'info::' . $template -> lang('information') . '<br>';
//echo 'info-ver::' . $template -> lang('information:version') . '<br>';
//echo 'info-ph-0::' . $template -> view -> lang('information:phone:0') . '<br>';

//echo print_r($template -> getRealPath(), 1) . '<br>';
//echo print_r($template -> view -> getPagePath(), 1) . '<br>';
//echo print_r($template -> view -> getCachePage(), 1) . '<br>';
//echo print_r($template -> view -> getBlockPath('home'), 1) . '<br>';

//echo print_r($template -> view -> setCachePages(true), 1) . '<br>';
//echo print_r($template -> view -> setCacheBlocks(true), 1) . '<br>';
echo print_r($template -> view -> includes('home'), 1) . '<br>';

$template -> view -> detect -> init();
//echo print_r($template -> view -> detect -> match('type', 'desktop'), 1) . '<br>';

// Задаем пути для рендеринга

$from = [
	$config -> get('path:templates') . $router -> template['name'] . DS,
	DS
];
$url = [
	'/' . Paths::clearSlashes($config -> get('url:assets:url')) . '/',
	'/' . $router -> template['name'] . '/'
];
$to = [
	$config -> get('path:site') . Paths::toReal(Paths::clearSlashes($url[0])) . DS,
	DS . Paths::toReal(Paths::clearSlashes($url[1])) . DS
];

$template -> view -> prepare($from, $to, $url);

unset($from, $to, $url);

// осуществляем рендеринг less файла
$result = $template -> view -> less('template', 'less');
echo $result;

// осуществляем рендеринг scss файла
$result = $template -> view -> scss('template', 'scss');
echo $result;

// осуществляем копирование css файла
$result = $template -> view -> css('template', 'css');
echo $result;

// осуществляем копирование js файла
$result = $template -> view -> js('template', 'js');
echo $result;

// сброс рендера
$template -> view -> render -> reset();

//$template -> view -> less -> init($from . 'less', $to . 'less');
//echo print_r($template -> view -> less -> less(), 1) . '<br>';

//$template -> view -> render -> init($from . 'less' . DS . 'temp.less', $to . 'less' . '\\' . 'temp.css');
//$template -> view -> render -> setHash();
//echo print_r($template -> view -> render, 1) . '<br>';

//$print = Display::getInstance();
//$print -> dump($user -> getData());
//echo '<hr>';
//$print -> dump($db);
//$print -> dump($uri);
//$print -> dump($state);
//$print -> dump($template);
//$print -> dump($router);
//
//exit;

?>