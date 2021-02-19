<?php defined('isENGINE') or die;

global $template;
global $content;

// включение кэширования в заголовках

header('Cache-Control: public');
header('Expires: ' . date('r', time() + 3 * TIME_HOUR));

// проверка даты последней модификации страницы
// если последняя модификация была давно, то вытаскиваем страницу из кэша
// если же нет, то читаем страницу заново

$cache = [
	'time' => null,
	'date' => null,
	'modified' => null
];

// установка даты и времени последнего изменения страницы
// для контента это время изменения контента
// для других страниц это время изменения шаблона (хотя, пожалуй, это не правильно)

if (objectGet('content', 'name')) {
	$cache['time'] = objectGet('content', 'first')['mtime'];
} elseif (file_exists($template -> path -> page)) {
	$cache['time'] = filemtime($template -> path -> page);
} elseif (!empty($template -> modified)) {
	$cache['time'] = $template -> modified;
} else {
	$cache['time'] = time();
}

$cache['date'] = gmdate('D, d M Y H:i:s \G\M\T', $cache['time']);

if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
	$cache['modified'] = strtotime(substr($_SERVER['HTTP_IF_MODIFIED_SINCE'], 5));
} elseif (isset($_ENV['HTTP_IF_MODIFIED_SINCE'])) {
	$cache['modified'] = strtotime(substr($_ENV['HTTP_IF_MODIFIED_SINCE'], 5));  
}

if ($cache['modified'] && $cache['modified'] >= $cache['time']) {
	// комментирование только на время теста
	header($_SERVER['SERVER_PROTOCOL'] . ' 304 Not Modified');
	exit;
} else {
	header('Last-Modified: '. $cache['date']);
}

// проверка состояния массива
//print_r($cache);
// проверка отображения заголовков
//echo '<pre>'; print_r(getallheaders()); echo '</pre>' . strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) . ' /// ' . $cache['time'];
//exit;

unset($cache);

?>