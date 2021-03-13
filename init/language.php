<?php

// Рабочее пространство имен

namespace is;

use is\Helpers\System;
use is\Helpers\Strings;
use is\Helpers\Objects;
use is\Helpers\Sessions;
use is\Helpers\Url;
use is\Helpers\Prepare;
use is\Model\Components\Path;
use is\Model\Components\Session;
use is\Model\Components\Uri;
use is\Model\Components\State;
use is\Model\Components\Config;
use is\Model\Components\Content;
use is\Model\Components\Display;
use is\Model\Components\Log;
use is\Model\Components\User;
use is\Model\Components\Language;
use is\Model\Database\Database;

// читаем user

$config = Config::getInstance();
$state = State::getInstance();
$user = User::getInstance();
$uri = Uri::getInstance();
$session = Session::getInstance();

$lang = Language::getInstance();
$lang -> init();

$db = Database::getInstance();
$db -> collection('languages');
$db -> driver -> addFilter('type', 'settings');
$db -> driver -> addFilter('name', 'default');
$db -> launch();

$lang -> setSettings( $db -> data -> getFirstData() );

$db -> clear();

// задаем массив возможных значений языка по приоритету
$config_lang = $config -> get('default:lang');

$array = [
	'uri' => null,
	'cookie' => Sessions::getCookie('lang'),
	'user' => $user -> getFieldsBySpecial('language'),
	'config' => $config_lang,
	'uri_first' => Objects::first($uri -> path['array'], 'value'),
	'uri_second' => Objects::n($uri -> path['array'], 1, 'value')
];

// проверяем язык из конфига

if (!$array['config'] || $array['config'] === true) {
	$array['config'] = $lang -> lang;
}

// проверяем язык из урла

$array['uri_first'] = $lang -> mergeLang($array['uri_first']);
$array['uri_second'] = $lang -> mergeLang($array['uri_second']);

if ($array['uri_first']) {
	$array['uri'] = $array['uri_first'];
	$uri -> route = Objects::reset( Objects::unfirst($uri -> route) );
	$uri -> path['array'] = Objects::reset( Objects::unfirst($uri -> path['array']) );
} elseif ($array['uri_second']) {
	$array['uri'] = $array['uri_second'];
	$uri -> route = Objects::reset( Objects::unn($uri -> route, 1) );
	$uri -> path['array'] = Objects::reset( Objects::unn($uri -> path['array'], 1) );
}

unset($array['uri_first'], $array['uri_second']);

// устанавливаем язык

$lang -> setLang( Objects::first( Objects::clear($array), 'value' ) );

// устанавливаем куки

Sessions::setCookie('lang', $lang -> lang);

// устанавливаем урл

if (
	System::type($config_lang, 'string') &&
	$lang -> lang !== $config_lang
) {
	$uri -> path['array'] = Objects::add($lang -> lang, $uri -> path['array']);
	$uri -> setFromArray();
}

//echo '<pre>';
//echo print_r($lang, 1);
//echo print_r($uri, 1);
//echo '</pre>';

?>