<?php defined('isPROCESS') or die;
	
	cookie('UID', $user -> uid);
	
	if (SECURE_SESSIONTIME) {
		$_SESSION['secure'] = true;
	}
	
	// сюда можно записывать переменные, которые нужно оставить в сессии
	// и таким образом частично защитить от перехвата
	
	// эта переменная указана только лишь для примера
	// $_SESSION['userid'] = time();
	
	$_SESSION['user'] = json_encode($user);
	$_SESSION['un'] = $data['name'];
	
	//if (empty($_SESSION['user'])) {
	//	$_SESSION['un'] = $data['name'];
	//}
	
	// читаем права пользователя и сохраняем их в сессии и в куках
	init('users', 'rights');
	
?>