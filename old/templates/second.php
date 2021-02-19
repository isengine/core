<?php defined('isENGINE') or die;

global $template;

// загружаем настройки шаблона

$settings = dbUse('templates:' . $template -> name, 'select', ['return' => 'alone']);

if (!empty($settings)) {
	$template -> created = $settings['ctime'];
	$template -> modified = $settings['mtime'];
	//$template -> settings = (object) $settings['data'];
	$template -> settings = (object) objectMerge((array)$template -> settings, $settings['data'], 'replace');
}

unset($settings);

// проверка на общее ограничение доступа к шаблону
// не путайте с ограничениями через роли в базе данных

if (objectIs($template -> settings -> secure)) {
	init('templates' . DS . 'init', 'secure');
}

// загружаем функции
// снова заметный прирост памяти на функции

init('functions', 'local');
init('functions', 'templates');
init('functions', 'math');

// инициализируем классы
// прироста памяти почти нет

if (defined('CORE_HTML') && CORE_HTML) {
	init('class', 'htmlelement');
}
if (defined('CORE_CONTENT') && CORE_CONTENT) {
	init('class', 'content');
}
if (defined('CORE_SHOP') && CORE_SHOP) {
	init('class', 'shop');
}

//require_once PATH_CORE . 'classes' . DS . 'htmlelement' . DS . 'htmlelement.php';
//require_once PATH_CORE . 'classes' . DS . 'content' . DS . 'content.php';

// в функции шаблона нужно прописать функцию вывода данных из языкового пакета
// это должна быть общая функция типа print

// загружаем библиотеки
init('libraries', 'first');

// инициализируем библиотеки
// огромный скачок памяти, самый большой за всю загрузку
//logging('memory is ' . round(memory_get_usage() / 1024, 2) . ' kb and in peak is ' . round(memory_get_peak_usage() / 1024, 2) . ' kb', 'memory_test_1');
init('libraries', 'second');
//logging('memory is ' . round(memory_get_usage() / 1024, 2) . ' kb and in peak is ' . round(memory_get_peak_usage() / 1024, 2) . ' kb', 'memory_test_2');

// загрузка языковых файлов
init('templates' . DS . 'init', 'languages');

// инициализируем новый компонент системы - контент
init('templates' . DS . 'init', 'content');

// подготавливаем скрипты и стили
init('templates' . DS . 'init', 'assets');

// создаем иконки в автоматическом режиме
init('templates' . DS . 'init', 'icons');

// загрузка seo
init('templates' . DS . 'init', 'seo');

// загрузка компонентов шаблона согласно опциям

// КЭШ
if (in('options', 'cache')) {
	init('templates' . DS . 'options', 'cache');
}

// composer
if (in('options', 'composer')) {
	init('templates' . DS . 'options', 'composer');
}

// здесь загружаем информацию о компьютере пользователя, если в параметрах шаблона есть свойство 'mobiledetect'
if (in('options', 'device')) {
	init('templates' . DS . 'options', 'device');
}

//print_r($template);

// загрузка шаблона
if ($template -> section) {
	require_once $template -> path -> page;
	// этот код и это условие были добавлены, чтобы механизм секций грузился правильно
	// вот вопрос, нужно ли теперь править код сессий в роутинге
	// и будет ли работать это новое решение в случае, когда сессия подключается к открытому шаблону, но с авторизованным пользователем
	// например, как в админке битрикс
	// к слову, сейчас это не работает
} elseif (file_exists(PATH_TEMPLATES . $template -> name . DS . 'template.php')) {
	require_once PATH_TEMPLATES . $template -> name . DS . 'template.php';
} else {
	//error('404', true);
}

?>