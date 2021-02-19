<?php defined('isENGINE') or die;

global $user;

if (DEFAULT_LANG && empty($user -> lang)) {
	
	global $userstable;
	$field = dbUse($userstable, 'filter', ['filter' => 'system:language', 'return' => 'alone']);
	
	if (!empty($field)) {
		
		$user -> lang = set($user -> data[$field], true);
		
		// также нам надо перезаписать данные пользователя в сессию
		
		$_SESSION['user'] = json_encode($user);
		
	}
	
	unset($field);
	
}

?>