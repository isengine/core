<?php

// Рабочее пространство имен

namespace is;

use is\Helpers\System;
use is\Helpers\Strings;
use is\Helpers\Objects;
use is\Helpers\Sessions;
use is\Helpers\Prepare;
use is\Model\Components\Session;
use is\Model\Components\Uri;
use is\Model\Components\State;
use is\Model\Components\Config;
use is\Model\Components\Display;
use is\Model\Components\Log;
use is\Model\Components\User;
use is\Model\Components\Router;
use is\Model\Databases\Database;

// читаем user

$uri = Uri::getInstance();
//$user = User::getInstance();
//$session = Session::getInstance();
//$state = State::getInstance();
$router = Router::getInstance();
$config = Config::getInstance();

// здесь расположен базовый обработчик роутинга

$path_array = $uri -> getRoute();
$path = $uri -> path['string'] ? '/' . $uri -> path['string'] : null;

// определяем, где в структуре мы находимся

// составляем путь

$route = null;

if (System::typeIterable($path_array)) {
	$find = Objects::find($path_array, $config -> get('url:data:rest'));
	$router -> route = System::set($find) ? Objects::get($path_array, 0, $find) : $path_array;
	unset($find);
	$route = Strings::join($router -> route, ':');
}

if ($route) {
	if (Objects::match($router -> structure -> getNames(), $route)) {
		$router -> current = $router -> structure -> getByName($route);
	} else {
		$state = State::getInstance();
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
	//$router -> current = null;
	$router -> current -> data['link'] = '/';
}

unset($path_array);

// сравниваем урл структуры с тем, который сейчас
// и если нет совпадения, то переназначаем текущий урл
// сохраняя при этом параметры строки

$link = $router -> current -> data['link'];

if ($path && !Strings::find($path, $link, 0)) {
	$state = State::getInstance();
	$state -> set('error', 404);
	$state -> set('reason', 'page not found in structure');
	$state -> set('section', Objects::first($router -> route, 'value'));
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