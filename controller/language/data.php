<?php

// Рабочее пространство имен

namespace is;

use is\Helpers\System;
use is\Helpers\Strings;
use is\Helpers\Objects;
use is\Helpers\Sessions;
use is\Helpers\Parser;
use is\Helpers\Local;
use is\Components\Session;
use is\Components\Uri;
use is\Components\State;
use is\Components\Config;
use is\Components\Display;
use is\Components\Log;
use is\Components\User;
use is\Components\Language;
use is\Components\Cache;
use is\Masters\Database;

// читаем user

$lang = Language::getInstance();
$config = Config::getInstance();

$cache = new Cache($config -> get('path:cache') . 'language' . DS);
$cache -> caching($config -> get('cache:language'));
$cache -> init($lang -> lang);

$data = $cache -> read();

if ($data) {
	
	$lang -> setData($data);
	
} else {
	
	$db = Database::getInstance();
	$db -> collection('languages');
	$db -> driver -> filter -> addFilter('parents', $lang -> lang);
	$db -> launch();
	
	$result = $db -> data -> getData();
	
	$db -> clear();
	
	if (System::typeIterable($result)) {
		foreach ($result as $item) {
			$k = $item -> getEntryKey('name');
			$i = $item -> getData();
			if ($i) {
				if ($k === $lang -> lang) {
					$lang -> mergeData($i);
				} else {
					$lang -> addData($k, $i);
				}
			}
			unset($k, $i);
		}
		unset($item);
	}
	
	unset($result);
	
}

$cache -> write( $lang -> getData() );

unset($cache, $data);

//echo '<pre>';
//echo print_r($result, 1);
//echo print_r($lang -> getData(), 1);
//echo print_r($lang, 1);
//echo print_r($uri, 1);
//echo '</pre>';

?>