<?php defined('isPROCESS') or die;

global $process;

// читаем базу процессов
// каждый процесс должен быть зарегистрирован
// если вы хотите ограничить определенные процессы для пользователей,
// то их вызов, чтение из базы данных, должны быть запрещены

// запускаем логику защищенного процесса
// логика такая - запускаем userPID и сверяем его с куками
// затем читаем установки системы
// если было ограничего число попыток или времени, мы читаем также из базы данных попытки, относящиеся к текущему процессу
// проверяем время последней попытки и число попыток
// далее:
// - если заданное число попыток было превышено, процесс не запускается
// - если заданное время с последнего запуска не прошло, процесс не запускается
// - если pid совпадает с уже записанным, то процесс не запускается
// - если было превышено число попыток с текущего pid за отведенное время, процесс не запускается

// ЕЩЕ МЫ МОЖЕМ СДЕЛАТЬ ПОДТВЕРЖДЕНИЕ ПРОЦЕССА
// например, через email
// системой генерируется код и отправляется по email, а хэш записывается в сессии
// пользователю выводится окно подтверждения
// если пароль совпал, то процесс разрешен и выполнение продолжается

if (!DEFAULT_USERS) {
	init('users', 'first');
}

$pid = userPID();

if (!$pid) {
	logging('bad pid query - may be is hack or change ip', 'bad pid query');
	return;
}

// данные защищенного доступа
// они хранятся в базе данных по идентификатору пользователя
// этот идентификатор - PID, который генерируется на основе UID, IP и AGENT
// если вы хотите ограничить доступ запрещением пустого агента или источника, рекомендуем задать настройку сайта SECURE_REQUEST

// злоумышленник может обойти систему защиты процесса сменой агента и ай-пи, например, таким образом можно накрутить счетчик до бесконечности
// в таком случае вам желательно ограничить число попыток или время доступа

// мы можем управлять следующими настройками защиты процесса:
// - задать число попыток для каждого pid, если не задано, то число попыток неограничено
// - задать время между попытками, в заданном промежутке после последней попытки, если истекло число попыток, больше попыток делать нельзя,
//   однако по истечении данного времени срабатывает разрешение и число попыток обнуляется

$attempts = dbUse('attempts:' . $pid, 'select', ['allow' => 'parent:' . $process -> set -> parent . '+' . $process -> set -> name, 'return' => 'alone']);

//echo '<hr>DEFAULT_USERS: ' . DEFAULT_USERS . '<br>PID: ' . print_r($pid, true) . '<hr>';
//echo '<hr>ATTEMPTS:' . print_r($attempts, true) . '<hr>';
//echo '<hr>PROCESS: ' . print_r($process -> set -> secure, true) . '<hr>';

// вычисление ошибок
// нужно сохранять именно этот порядок
$error = [];

// ошибка превышения заданной паузы
// работает только если в настройках задано определение паузы

if (!empty($process -> set -> secure['pause'])) {
	
	// преобразуем время в абсолютные единицы
	$process -> set -> secure['pause'] = dataParseTime($process -> set -> secure['pause']);
	
	if (!empty($attempts['data']['time']) && (int) $attempts['data']['time'] + (int) $process -> set -> secure['pause'] > $process -> time) {
		$error[] = 'pause';
	}
	
}

// ошибка превышения числа запросов
// работает только если в настройках задано определение числа запросов

if (!empty($process -> set -> secure['count'])) {
	
	if (!empty($attempts['data']['count']) && (int) $attempts['data']['count'] >= (int) $process -> set -> secure['count']) {
		$error[] = 'count';
	} else {
		// нужно увеличить запрос на единицу
		$attempts['data']['count']++;
	}
	
}

// если в настройках задана чистка попыток после определенного времени,
// проверяем истекшее время и чистим попытки

if (!empty($process -> set -> secure['clear']) && in_array('count', $error)) {
	
	// преобразуем время в абсолютные единицы
	$process -> set -> secure['clear'] = dataParseTime($process -> set -> secure['clear']);
	
	if (!empty($attempts['data']['time']) && (int) $attempts['data']['time'] + (int) $process -> set -> secure['clear'] < $process -> time) {
		$attempts['data']['count'] = 0;
		$error = null;
	}
	
}

// ошибка неавторизованного пользователя
// работает только если в настройках задана опция только для авторизованных пользователей

if (!empty($process -> set -> secure['authorised'])) {
	
	global $user;
	
	if (empty($user -> uid)) {
		//echo 'noauthorised';
		$error[] = 'noauthorised';
	}
	
}

// завершаем процесс

// если в процессе задано сохранение кук, то записываем эти куки
if (!empty($process -> set -> secure['cookie'])) {
	if ($process -> set -> secure['cookie'] === true) {
		$attempts['data']['cookie'] = cookie(true, true);
	} else {
		$cookie = dataParse($process -> set -> secure['cookie']);
		foreach ($cookie as $item) {
			$attempts['data']['cookie'][$item] = cookie($item, true);
		}
		unset($item);
	}
}

// нужно записать текущее время как последнее
$attempts['data']['time'] = $process -> time;

// и назначить закрытие процесса
if (empty($process -> close)) {
	$process -> close = $process -> set -> secure['close'];
}

// и если в настройках задано продление времени для процесса, смещаем время

if (!empty($process -> set -> secure['time'])) {
	
	// преобразуем время в абсолютные единицы
	$process -> set -> secure['time'] = dataParseTime($process -> set -> secure['time']);
	
	$process -> hash += (int) $process -> set -> secure['time'];
	
}

// обрабатываем ошибки

if (!empty($error)) {
	
	global $uri;
	if (empty($uri -> query -> string)) {
		$uri -> query -> string = '?';
	} else {
		$uri -> query -> string .= '&';
	}
	$uri -> query -> string .= 'process_error=' . objectToString($error, '.');
	
	$process -> set -> secure = null;
	unset($attempts, $pid);
	logging('bad attempts on query -- ' . objectToString($error, ', '), 'bad attempts on query');
	return false;
	
}

// записываем очередную попытку в базу данных

$process -> set -> secure = [
	'id' => null,
	'name' => $pid,
	'parent' => [$process -> set -> parent, $process -> set -> name],
	//'data' => json_encode($attempts['data'])
	'data' => $attempts['data']
];

//print_r($process -> set -> secure);
//$a = dbUse('attempts', 'write', [$process -> set -> secure]);
//echo '[dbupdate -- ' . (empty($a) ? 'false' : 'ok') . ']';

unset($attempts, $pid);

?>