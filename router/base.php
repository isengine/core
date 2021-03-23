<?php

// Рабочее пространство имен

namespace is;

use is\Helpers\System;
use is\Helpers\Strings;
use is\Helpers\Objects;
use is\Helpers\Sessions;
use is\Helpers\Prepare;
use is\Helpers\Paths;
use is\Model\Components\Path;
use is\Model\Components\Session;
use is\Model\Components\Uri;
use is\Model\Components\State;
use is\Model\Components\Config;
use is\Model\Components\Content;
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

// определяем, где в структуре мы находимся

$path_array = $uri -> path['array'];

if (System::typeIterable($path_array)) {
	
	$find = Objects::find($path_array, $config -> get('url:data:name'));
	$router -> route = Objects::get($path_array, 0, $find);
	unset($find);
	
	$route = Strings::join($router -> route, ':');
	
	if (Objects::match($router -> structure -> getNames(), $route)) {
		$router -> current = $router -> structure -> getByName($route);
	} else {
		$state = State::getInstance();
		$state -> set('error', 404);
		$state -> set('reason', 'page not found in structure');
	}
	
} else {
	$router -> current = $router -> structure -> getByName( $router -> getHome() );
}

unset($path_array);

// сравниваем урл структуры с тем, который сейчас
// и если нет совпадения, то переназначаем текущий урл
// сохраняя при этом параметры строки

$link = $router -> current -> data['link'];
$path = $uri -> path['string'] ? '/' . $uri -> path['string'] : null;

if ($path && !Strings::find($path, $link, 0)) {
	$state = State::getInstance();
	$state -> set('error', 404);
	$state -> set('reason', 'page not found in structure');
	$state -> set('section', Objects::first($router -> route, 'value'));
	
	//if (System::typeIterable($uri -> data)) {
	//	$f = Strings::find($link, '#');
	//	if ($f) {
	//		$fragment = Strings::get($link, $f);
	//		$link = Strings::get($link, 0, $f);
	//	}
	//	if (
	//		$config -> get('url:data:path') &&
	//		$config -> get('url:data:name')
	//	) {
	//		$string = Objects::add(
	//			[$config -> get('url:data:name')],
	//			Objects::unpairs($uri -> data)
	//		);
	//		$link .= Strings::join($string, '/');
	//	} elseif (
	//		$config -> get('url:data:query')
	//	) {
	//		$uri -> setQueryString( Objects::merge($uri -> query['array'], $uri -> data) );
	//	}
	//}
	//$uri -> url = Paths::absoluteUrl($link) . $uri -> query['string'] . $fragment;
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