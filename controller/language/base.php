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
use is\Model\Components\Language;
use is\Model\Databases\Database;

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
$db -> driver -> filter -> addFilter('type', 'settings');
$db -> driver -> filter -> addFilter('name', 'default');
$db -> launch();

$lang -> setSettings( $db -> data -> getFirstData() );

foreach ($lang -> settings as $key => $item) {
	$lang -> addList($key);
	$lang -> addList($key, $item['alias']);
	$lang -> addCode($key, $item['code']);
}
unset($key, $item);

$db -> clear();

// задаем массив возможных значений языка по приоритету
$config_lang = $config -> get('default:lang');

$array = [
	'uri' => null,
	'cookie' => Sessions::getCookie('lang'),
	'user' => $user -> getFieldsBySpecial('language'),
	'config' => $config_lang,
	'uri_first' => $uri -> getPathArray(0),
	'uri_second' => $uri -> getPathArray(1)
];

// проверяем язык из конфига

if (!$array['config'] || $array['config'] === true) {
	$array['config'] = $lang -> lang;
}

// проверяем язык из урла

$array['uri_first'] = $lang -> mergeLang($array['uri_first']);
$array['uri_second'] = $lang -> mergeLang($array['uri_second']);

if (
	$array['uri_first'] ||
	$array['uri_second']
) {
	if ($array['uri_first']) {
		$array['uri'] = $array['uri_first'];
		$uri -> unPathArray(0);
	} elseif ($array['uri_second']) {
		$array['uri'] = $array['uri_second'];
		$uri -> unPathArray(1);
	}
	$uri -> language = $array['uri'];
}

unset($array['uri_first'], $array['uri_second']);

// устанавливаем язык

$lang -> setLang( Objects::first( Objects::clear($array), 'value' ) );

// устанавливаем куки

Sessions::setCookie('lang', $lang -> lang);

// устанавливаем урл

if (
	System::type($config_lang, 'string') &&
	$config_lang !== $lang -> lang
) {
	$uri -> language = $lang -> lang;
} elseif (
	!$config_lang ||
	$config_lang === $uri -> language
) {
	$uri -> language = null;
}

$uri -> setFromArray();

if ($uri -> url !== $uri -> original) {
	$state -> set('reload', 'temporary');
}

//echo '<pre>';
//echo print_r($lang, 1);
//echo '</pre>';

?>