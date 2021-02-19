<?php defined('isPROCESS') or die;

//print_r($query -> data);

$query -> var['path'] = __DIR__ . DS . 'items' . DS . $query -> data -> name . '.php';

if (file_exists($query -> var['path'])) {
	require_once $query -> var['path'];
}

exit;

?>