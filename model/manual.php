<?php

// Рабочее пространство имен

namespace is;

$list = file_get_contents( DR . 'config' . DS . 'classes.ini' );
$list = json_decode($list, true);

if (!empty($list)) {
	foreach ($list as $item) {
		$item = DR . 'vendor' . DS . str_replace(['\\', '/', ':'], DS, $item);
		//echo '[' . DR . 'vendor' . DS . '<br>';
		require $item;
	}
	unset($item);
}

?>