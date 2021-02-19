<?php defined('isENGINE') or die;

global $process;

// читаем базу процессов
// каждый процесс должен быть зарегистрирован
// если вы хотите ограничить определенные процессы для пользователей,
// то их вызов, чтение из базы данных, должны быть запрещены

// готовим пути процесса

if (strpos($process -> set -> type, 'module') !== false) {
	$process -> set -> path = PATH_MODULES . $process -> set -> vendor . DS . $process -> set -> parent . DS . 'processor' . DS;
} elseif ($process -> set -> type === 'custom') {
	$process -> set -> path = DEFAULT_CUSTOM ? PATH_CUSTOM . 'core' . DS . 'processor' . DS . $process -> set -> parent . DS : null;
} else {
	$process -> set -> path = PATH_CORE . 'processor' . DS . $process -> set -> parent . DS;
}

/*
switch($process -> set -> type) {
	case 'module':
		$process -> set -> path = PATH_MODULES . $process -> set -> vendor . DS . $process -> set -> parent . DS . 'processor' . DS;
		break;
	case 'custom':
		$process -> set -> path = DEFAULT_CUSTOM ? PATH_CUSTOM . 'core' . DS . 'processor' . DS . $process -> set -> parent . DS : null;
		break;
	default:
		$process -> set -> path = PATH_CORE . 'processor' . DS . $process -> set -> parent . DS;
		break;
}
*/

if (!empty($process -> set -> path)) {
	$process -> set -> path .= $process -> set -> name . DS . 'init.php';
}

if ($process -> data) {
	if (is_object($process -> data)) {
		$process -> data = (array) $process -> data;
	} elseif (!is_array($process -> data)) {
		$process -> data = [ 'default' => $process -> data ];
	}
}

//print_r($process);
//exit;

// Проверка правильности запроса
// Проверка осуществляется по наличию хэша в запросе и соответствия его времени не больше 10 минут
// Либо должен быть специальный хэш - начинаться на = и состоять из 10 знаков, но такой хэш работает только в режиме разработки
// В случае тестирования, данную проверку можно отключить, просто закомментировав код ниже

if (empty($process -> set -> secure['time'])) {
	$process -> hash += (int) SECURE_PROCESSTIME;
}

if (!(
	$process -> name === 'xml' ||
	$process -> hash > $process -> time ||
	(
		DEFAULT_MODE === 'develop' &&
		strlen($process -> hash) === 11 &&
		substr($process -> hash, 0, 1) === '='
	)
)) {
	error('403', true, 'bad hash query - may be is hack');
}

// еще одна проверка в качестве защиты от ботов - должно присутствовать поле 'check'
// но при этом оно должно быть пустым, не заполненным
if (!$process -> check) {
	error('403', true, 'bad check field in query - bot protection');
}

// защита от CSRF-атаки

/*
print_r($_SESSION['csrf']);
echo '<br>';
print_r($process -> csrf);
echo '<br>';
echo '[' . password_verify($_SESSION['csrf'], $process -> csrf) . ']';
echo '<br>';
*/

if (
	SECURE_CSRF &&
	(
		empty($process -> csrf) ||
		(
			!empty($process -> csrf) &&
			!csrf($process -> csrf)
		)
	)
) {
	error('403', true, 'bad csrf token in query - may be is hack');
}

// проверяем тип процесса, системный ли он
// и выставляем значение константы isSYSTEM

if (defined('isSYSTEM')) {
	error('403', false, 'is hack attempt to change system constant isSYSTEM');
}

if ($process -> set -> type === 'system') {
	define('isSYSTEM', true);
} else {
	define('isSYSTEM', false);
}

// переинициализируем драйвера на запись в базу данных
init('drivers', 'second');

// записываем попытку в базу данных
if (!empty($process -> set -> secure)) {
	dbUse('attempts', 'write', [$process -> set -> secure]);
}

// вызываем процесс
if ($process -> set -> path && file_exists($process -> set -> path)) {
	require_once $process -> set -> path;
} else {
	error('403', true, 'needed processor file is missing');
}

?>