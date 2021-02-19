<?php defined('isPROCESS') or die;

if (!empty($process -> data['default']) && !empty($_SESSION)) {
	userUnset();
	reload();
	//header('Location: /');
}

?>