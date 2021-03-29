<?php

// Рабочее пространство имен

namespace is;

spl_autoload_register(function($class) {
	
	$array = explode('\\', $class);
	$first = array_shift($array);
	
	array_shift($array);
	
	$file = mb_strtolower(array_pop($array)) . '.php';
	$folder = __DIR__ . DS . mb_strtolower(implode(DS, $array));
	
	$result = str_replace('\\', DS, $folder . DS . $file);
	
	//echo '[' . $result . ' --- ' . $class . ']<br>';
	
	if (file_exists($result)) {
		require $result;
	}
	
});

?>