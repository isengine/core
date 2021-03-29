<?php

// Рабочее пространство имен

namespace is;

// загрузка фреймворка и регистрация классов

$autoload = DR . 'vendor' . DS . 'autoload.php';
$framework = DR . 'vendor' . DS . 'isengine' . DS . 'framework' . DS . 'php' . DS . 'init.php';

if (file_exists($autoload)) {

// автоматическая

require_once $autoload;

} else {

// полуавтоматическая

require_once $framework;

spl_autoload_register(function($class) {
	
	$array = explode('\\', $class);
	array_shift($array);
	
	$file = mb_strtolower(array_pop($array)) . '.php';
	$folder = __DIR__ . DS . mb_strtolower(implode(DS, $array));
	
	$result = str_replace('\\', DS, $folder . DS . $file);
	
	if (file_exists($result)) {
		require $result;
	}
	
});

}

unset($autoload, $framework);

?>