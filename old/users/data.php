<?php defined('isENGINE') or die;

global $user;

// проверяем сессию на наличие данных пользователя
// если какие-то данные пусты, мы будем читать их заново

if (!empty($_SESSION['user'])) {
	
	//logging('USERDATA FROM SESSION!');
	//echo '<br>USERDATA FROM SESSION!<hr><br>';
	$userbase = json_decode($_SESSION['user'], true);
	
} elseif (!empty($_SESSION['un'])) {
	
	//logging('USERDATA FROM DB!');
	//echo '<br>USERDATA FROM DB!<hr><br>';
	$userbase = userFind($_SESSION['un']);
	
} else {
	
	//echo '<br>GLOBAL ERROR!<hr><br>';
	userUnset();
	error('403', true, 'user data or user name not set in session and cannot be read from database');
	
}

/*
print_r($user);
echo '<br><br>';
print_r($userbase);
*/

$user = objectMerge($user, (object) $userbase, 'replace');

if (empty($_SESSION['user'])) {
	
	// если нет записи данных пользователя в сессии,
	// это значит, что мы прочли их из базы данных,
	// и теперь их нужно записать в сессию
	
	$_SESSION['user'] = json_encode($userbase);
	
}

if (empty($_SESSION['un'])) {
	$_SESSION['un'] = $user -> name;
}

unset($userbase);
//unset($_SESSION['user']);

?>