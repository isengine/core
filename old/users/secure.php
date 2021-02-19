<?php defined('isENGINE') or die;

global $user;
global $userstable;
//print_r($user);
//print_r($userstable);

if (
	isALLOW &&
	!empty($_SESSION['allow']) &&
	cookie('allow', true)
) {
	
	if ($_SESSION['allow'] === md5(cookie('allow', true))) {
		$user -> allow = json_decode(cookie('allow', true), true);
	} else {
		userUnset();
		error('403', false, 'allow for \'' . $user -> name . '\' user not match hash - may be is hack');
	}

} else {
	
	$allow = ['allow', 'allowip', 'allowagent'];
	
	foreach ($allow as $item) {
		$field = dbUse($userstable, 'filter', ['filter' => 'system:' . $item, 'return' => 'alone']);
		if (!empty($field)) {
			if (set($user -> data[$field])) {
				$user -> allow[$item] = $user -> data[$field];
			}
		}
	}
	
	unset($item, $field, $allow);
	
	$_SESSION['allow'] = md5(json_encode($user -> allow));
	cookie('allow', json_encode($user -> allow));
	
}

?>