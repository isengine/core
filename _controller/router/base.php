<?php

// Рабочее пространство имен

namespace is;

use is\Helpers\System;
use is\Helpers\Strings;
use is\Helpers\Objects;
use is\Helpers\Sessions;
use is\Helpers\Prepare;
use is\Helpers\Local;
use is\Components\Session;
use is\Components\Uri;
use is\Components\State;
use is\Components\Config;
use is\Components\Display;
use is\Components\Log;
use is\Components\User;
use is\Components\Router;
use is\Parents\Entry;
use is\Masters\Database;

// читаем user

$uri = Uri::getInstance();
//$user = User::getInstance();
//$session = Session::getInstance();
$state = State::getInstance();
$router = Router::getInstance();
$config = Config::getInstance();

// здесь расположен базовый обработчик роутинга

$path = $uri -> path['string'] ? '/' . $uri -> path['string'] : null;

// определяем, где в структуре мы находимся

$route = Strings::join($uri -> getRoute(), ':');

// составляем путь

if ($route) {
	if (Objects::match($router -> structure -> getNames(), $route)) {
		$router -> current = $router -> structure -> getByName($route);
	} else {
		$state -> set('error', 404);
		$state -> set('reason', 'page not found in structure');
	}
} else {
	// раньше этого условия не было,
	// но теперь мы избавляемся от определения домашней страницы в структуре
	// проблема возникает только с определением и разделением:
	//   домашней страницы сайта,
	//   главной страницы шаблона
	//   секции шаблона, к которому нет доступа
	$router -> current = new Entry;
	$router -> current -> addData('link', '/');
}

// сравниваем урл структуры с тем, который сейчас
// и если нет совпадения, то переназначаем текущий урл
// сохраняя при этом параметры строки

$link = $router -> current -> data['link'];

//echo $link . '<br>';
//echo $path . '<br>';

if ($path && !Strings::find($path, $link, 0)) {
	$state -> set('error', 404);
	$state -> set('reason', 'page not found in structure');
}

// секции

if ($state -> get('error') === 404) {
	$section = Objects::first($uri -> getRoute(), 'value');
	if (
		$section &&
		Local::matchFolder($config -> get('path:templates') . $section)
	) {
		$state -> set('section', $section);
	}
}

// во-первых, мы должны разобрать урл, определить, где в структуре мы находимся
// задать путь роутинга и определить текущие настройки
// настройки авто роутинга:
//  шаблон по-умолчанию,
//  автоматическое определение шаблона по первому родителю, если такой шаблон есть
//  создание хэша по времени для файлов и автоматическое обновление кэша по хэшу папки шаблона

// мы будем определять и записывать
// шаблон
// путь - согласно uri path array, но он не будет повторять его точь-в-точь
// т.к. там будут определены папки, которые надо загрузить
// и параметры загрузки - из данных uri data и, возможно, из uri query
// также
// у нас раньше было понятие секции - это часть шаблона,
// которая может быть встроена в другой шаблон
// и также есть секция по-умолчанию default,
// которая должна открываться при недоступном шаблоне
// например, если вы запрашиваете вход в админку
// теперь
// благодаря использованию ооп мы можем поменять подход
// например, загружать в секции стили и скрипты шаблона секции
// но
// это уже больше относится ко вью
// однако
// нам нужно будет посмотреть подход к шаблонизации
// например, теперь мы можем использовать общие компоненты шаблона,
// рендеринг и копирование стилей, скриптов, шрифтов, библиотек в папку ассетов из папки шаблона
// и прочие функции
// так мы сможем сделать шаблон простым для создания, адаптации любого html-шаблона
// и установки, переноса из одного проекта в другой

//echo '<pre>';
//echo print_r($uri -> url, 1) . '<br>';
//echo print_r($uri -> original, 1) . '<br>';
//echo print_r($uri, 1);
//echo print_r($router, 1);
//echo '</pre>';

?>