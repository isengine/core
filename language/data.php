<?php

// Рабочее пространство имен

namespace is;

use is\Helpers\System;
use is\Helpers\Strings;
use is\Helpers\Objects;
use is\Helpers\Sessions;
use is\Helpers\Parser;
use is\Helpers\Local;
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
use is\Model\Databases\Database;

// читаем user

$lang = Language::getInstance();

$config = Config::getInstance();
$cache = $config -> get('path:cache') . 'language' . DS . $lang -> lang . '.ini';
$data = Local::readFile($cache);

if ($data) {
	
	$data = Parser::fromJson($data);
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
	
	$data = Parser::toJson($lang -> getData(), true);
	Local::createFile($cache);
	Local::writeFile($cache, $data, 'replace');
	
}

unset($cache, $data);

//echo '<pre>';
//echo print_r($result, 1);
//echo print_r($lang -> getData(), 1);
//echo print_r($lang, 1);
//echo print_r($uri, 1);
//echo '</pre>';

?>