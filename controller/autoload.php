<?php

// Рабочее пространство имен

namespace is;

$path = realpath(__DIR__ . DS . DP . DP . DP) . DS;

$autoload = $path . 'autoload.php';

if (file_exists($autoload)) {
	
	// auto loading
	require_once $autoload;
	
} else {
	
	// manual loading
	$autoload = $path . 'isengine' . DS . 'framework' . DS . 'php' . DS . 'init.php';
	require_once $autoload;
	
	$list = file_get_contents( DR . 'config' . DS . 'classes.ini' );
	$list = json_decode($list, true);
	
	if (!empty($list)) {
		foreach ($list as $item) {
			$item = $path . str_replace(['\\', '/', ':'], DS, $item) . '.php';
			require_once $item;
		}
		unset($item);
	}
	
	unset($list, $path);
	
}


?>