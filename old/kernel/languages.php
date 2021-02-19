<?php defined('isENGINE') or die;

use is\Functions\System;

global $lang;

if (DEFAULT_LANG) {
	
	global $uri;
	global $user;
	
	$lang = (object) [
		'settings' => dbUse('langcodes', 'select', true),
		'list' => [],
		'code' => '',
		'lang' => '',
		'data' => []
	];
	
	$lang -> list = array_keys($lang -> settings);
	
	// определяем язык по приоритету
	
	if (in_array(reset($uri -> path -> array), $lang -> list)) {
		
		// если lang задан в uri.path и он совпадает с одним из языков системы
		
		$lang -> lang = array_shift($uri -> path -> array);
		$uri -> path -> string = !empty($uri -> path -> array) ? objectToString($uri -> path -> array, '/') . '/' : '';
		
		// переадресация, если была вызвана страница, которая содержит в адресе некорректный запрос языка
		// например:
		//   если исходный язык системы определяется автоматически, а вы пытаетесь его изменить
		//   * в этом случае изменять язык на сайте лучше через куки
		//   если исходный язык системы равен языку страницы, в таком случае адрес страницы не должен содержать язык
		//   * вообще эта проверка заложена раньше, при проверке url, т.к. там меньше нагрузка на систему
		//     здесь же эта проверка просто на подстраховку
		
		// некоторой защитой от нагрузки выступает код 301, который сообщает браузеру, что перенаправление постоянное
		// однако это вряд ли защитит от ddos-атак - в таком случае, нужно отслеживать источник нагрузки и блокировать по ip
		
		if (
			DEFAULT_LANG === true ||
			DEFAULT_LANG === $lang -> lang
		) {
			
			$uri -> url = $uri -> site . $uri -> path -> string . $uri -> path -> file . $uri -> query -> string;
			
			if (DEFAULT_MODE === 'develop') { logging('the system was redirected because url contains invalid language request'); }
			
			reload($uri -> url, 301);
			
			//header($_SERVER['SERVER_PROTOCOL'] . ' 301 Moved Permanently', true, 301);
			//header('Location: ' . $uri -> url);
			//exit;
			
		}
		
	} elseif (DEFAULT_USERS && !empty($user -> lang) && in_array($user -> lang, $lang -> list)) {
		
		// если lang задан в user.lang и он совпадает с одним из языков системы
		$lang -> lang = $user -> lang;
		
	} elseif (cookie('LANG', true) && in_array(cookie('LANG', true), $lang -> list)) {
		
		// если lang задан в cookie
		$lang -> lang = cookie('LANG', true);
		
	} elseif (DEFAULT_LANG !== true) {
		
		// если lang задан через настройки
		$lang -> lang = DEFAULT_LANG;
		
	} else {
		
		// ну и наконец последний вариант - смотрим язык из браузера и настроек системы
		$lang -> lang = funcLang_GetBestMatch($lang -> settings, reset($lang -> list));
		
	}
	
	if (!empty(cookie('LANG', true))) {
		cookie('LANG', $lang -> lang);
	}
	
	if (DEFAULT_LANG !== true && $lang -> lang !== DEFAULT_LANG) {
		
		$uri -> path -> base = $lang -> lang . '/';
		$uri -> url = $uri -> site . $uri -> path -> base . $uri -> path -> string . $uri -> path -> file . $uri -> query -> string;
		
	}
	
	$lang -> code = strtolower(set($lang -> settings[$lang -> lang]['code']) ? $lang -> settings[$lang -> lang]['code'] : $lang -> lang);
	
} else {
	$lang = null;
}

//print_r($uri);
//print_r($lang);

function funcLang_GetBestMatch($set, $default) {
	
	$list = [
		'langs' => null,
		'arr' => isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? strtolower($_SERVER['HTTP_ACCEPT_LANGUAGE']) : null
	];
	
	if (preg_match_all('/([a-z]{1,8}(?:-[a-z]{1,8})?)(?:;q=([0-9.]+))?/', $list['arr'], $list['arr'])) {
		$list['langs'] = array_combine($list['arr'][1], $list['arr'][2]);
		foreach ($list['langs'] as $key => $item) {
			$list['langs'][$key] = $item ? $item : 1;
		}
		unset($key, $item);
		arsort($list['langs'], SORT_NUMERIC);
	} else {
		$list['langs'] = [];
	}
	
	$list['arr'] = [];
	
	foreach ($set as $key => $item) {
		$list['arr'][$key] = $key;
		if (!empty($item['alias'])) {
			$list['arr'] = array_merge($list['arr'], array_fill_keys(datasplit($item['alias']), $key));
		}
	}
	unset($key, $item);
	
	foreach ($list['langs'] as $key => $item) {
		$key = strtok($key, '-');
		if (isset($list['arr'][$key])) {
			return $list['arr'][$key];
		}
	}
	unset($key, $item);
	
	return $default;
	
}

?>