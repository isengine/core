<?php defined('isENGINE') or die;

global $user;
global $userstable;
//print_r($user);
//print_r($userstable);

// обработчик ошибок при проверке

// пока здесь только отчеты в логах, ну кроме ошибки за бан
// нужно сделать вывод или отправку уведомлений пользователю
// а также механизм добавления новых записей в базу данных
// и подтверждения у пользователя на их добавление с уведомлением по e-mail,
// вводом капчи - ну в общем как положено
// возможно, это придется сделать через шаблон ошибки или через шаблон восстановления доступа

if (!empty($user -> allow['allow'])) {
	
	// проверка на бан
	error('403', false, 'security user verification - user are banned');
	
} else {
	
	$allow = [];
	
	if (
		!empty($user -> allow['allowip']) &&
		ipRange($user -> ip, $user -> allow['allowip'])
	) {
		
		// проверка на присутствие текущего ip в списке разрешенных
		$allow['ip'] = true;
		
	}
	
	if (
		!empty($user -> allow['allowagent']) &&
		in_array(md5(USER_AGENT), $user -> allow['allowagent'])
	) {
		
		// проверка на присутствие текущего хэша агента в списке разрешенных
		$allow['agent'] = true;
		
	}
	
	if ($allow['ip'] && !$allow['agent'] && !isALLOW) {
		
		if (LOG_MODE === 'panic' || LOG_MODE === 'warning') {
			logging('security user verification - unknown agent but known ip, agent will be added in list');
		}
		
		$user -> allow['allowagent'][] = md5(USER_AGENT);
		$_SESSION['allow'] = md5(json_encode($user -> allow));
		cookie('allow', json_encode($user -> allow));
		
		// сюда не хватает записи о перезаписи $user -> allow['allowagent'] в базу данных
		// он уже массив, так что никаких дополнительных условий делать не нужно
		// разве только узнать имя поля в базе данных пользователя
		
	} elseif (!$allow['ip'] && $allow['agent'] && !isALLOW) {
		
		if (LOG_MODE === 'panic' || LOG_MODE === 'warning') {
			logging('security user verification - unknown ip but known agent, ip will be added in list with extended diapason');
		}
		
		$user -> allow['allowip'][] = md5($user -> ip);
		$_SESSION['allow'] = md5(json_encode($user -> allow));
		cookie('allow', json_encode($user -> allow));
		
		// сюда не хватает записи о перезаписи $user -> allow['allowip'] в базу данных
		// он уже массив, так что никаких дополнительных условий делать не нужно
		// разве только узнать имя поля в базе данных пользователя
		
	} elseif (!$allow['ip'] && !$allow['agent']) {
		logging('security user verification - unknown ip and agent, user must be notified and added this in lists');
	}

	unset($allow);
	
}

?>