<?php

// Рабочее пространство имен

namespace is;

use is\Helpers\System;
use is\Helpers\Strings;
use is\Helpers\Objects;
use is\Helpers\Parser;
use is\Helpers\Sessions;
use is\Helpers\Prepare;
use is\Model\Components\Session;
use is\Model\Components\Uri;
use is\Model\Components\State;
use is\Model\Components\Config;
use is\Model\Components\Display;
use is\Model\Components\Log;

// читаем uri

$config = Config::getInstance();
$state = State::getInstance();
$uri = Uri::getInstance();

// ссылки для удобства работы с кодом

$file = &$uri -> file;
$folder = &$uri -> folder;
$route = &$uri -> path['array'];

// Если вы используете REST
// НАСТОЯТЕЛЬНО НЕ РЕКОМЕНДУЕТСЯ ВКЛЮЧАТЬ
// ПРЕОБРАЗОВАНИЕ ПАПОК В ФАЙЛЫ
// в противном случае не гарантируется корректная работа
// ядра системы и роутинга

// ОБРАБОТКА ПАПОК
// поведение - преобразует папки в файлы с заданным расширением
// допустимые значения:
// false - запретить преобразование, папки остаются папками
// true - разрешить преобразование, папки переименовываются в файлы
// folders_index - папки остаются папками, но в них задается индексный файл
// дополняет действие folders_convert, использовать без него не получится
// folders_extension задает расширение, можно указать только одно

$folders_convert   = $config -> get('router:folders:convert');
$folders_index     = $config -> get('router:folders:index');
$folders_extension = $config -> get('router:folders:extension');

//$folders_convert   = $_GET['d_conv'];
//$folders_index     = $_GET['d_idx'];
//$folders_extension = $_GET['d_ext'];

if (!$folders_extension) { $folders_extension = 'php'; }

// ?d_conv=&d_idx=&d_ext=

// ОБРАБОТКА ФАЙЛОВ
// поведение - преобразует файлы с заданным расширением в папки
// допустимые значения:
// false - запретить преобразование, файлы остаются файлами
// true - разрешить преобразование, файлы переименовываются в папки
// files_index - индексные файлы преобразуются в папки
// можно использовать без folders_convert
// при совмещении с files_convert можно комбинировать различные поведения
// files_extension - расширения файлов для обработки, может принимать массив значений
// files_convert - расширения файлов, которые будут преобразовываться в заданное
// также может принимать массив значений
// если заданное расширение является массивом, то преобразуются в первое по списку

$files_convert = $config -> get('router:files:convert');
$files_index   = $config -> get('router:files:index');
$files_ext     = $config -> get('router:files:extension');

//$files_convert = $_GET['f_conv'];
//$files_index   = $_GET['f_idx'];
//$files_ext     = $_GET['f_ext'];

if (!$files_ext) { $files_ext = 'php'; }
$files_extension = Parser::fromString($files_ext, ['clear' => true]);

// ?f_conv=&f_idx=&f_ext=

// КОНВЕРТАЦИЯ РАСШИРЕНИЙ ФАЙЛОВ
// поведение - преобразует файлы с заданными расширениями в файлы с другим расширением
// имеет приоритет над обработкой
// для активации нужно указать оба параметра
// convert_from может принимать массив значений

$convert_fr = $config -> get('router:convert:from');
$convert_to = $config -> get('router:convert:to');

//$convert_fr = $_GET['c_from'];
//$convert_to = $_GET['c_to'];

$convert_from = Parser::fromString($convert_fr, ['clear' => true]);

// ?c_from=&c_to=

// ИНДЕКСНЫЙ ФАЙЛ
// название индексного файла без расширения
// по-умолчанию, index

$index_file = $config -> get('router:index');
if (!$index_file) { $index_file = 'index'; }

// СТАНДАРТНЫЕ ШАБЛОНЫ ПОВЕДЕНИЙ:
// 1. Все папки преобразуются в файлы
// 1.1. в файлы php
// 1.1.1. >>> без индексных
// 1.1.2. >>> с индексными
// 1.2. в файлы html, а php запрещены
// 1.2.1. >>> без индексных
// 1.2.2. >>> с индексными
// 1.3. в файлы html, а php и htm также конвертируются в html
// 1.3.1. >>> без индексных
// 1.3.2. >>> с индексными
// 2. Файлы преобразуются в папки
// 2.1. только файлы php
// 2.1.1. >>> с учетом индексных
// 2.1.2. >>> без учета индексных
// 2.1.3. >>> только индексные
// 2.2. только файлы html
// 2.2.1. >>> с учетом индексных
// 2.2.2. >>> без учета индексных
// 2.2.3. >>> только индексные
// 2.3. файлы html, а php и htm также конвертируются в html
// 2.3.1. >>> с учетом индексных
// 2.3.2. >>> без учета индексных
// 2.3.3. >>> только индексные

// ТЕСТИРУЕМЫЕ ШАБЛОНЫ ПОВЕДЕНИЙ:
// 1.1.2. Папки преобразуются в файлы php, с индексными
//        ?d_conv=1&d_idx=1&d_ext=php
// 1.2.1. Папки преобразуются в файлы html, без индексных, а php запрещены
//        ?d_conv=1&d_idx=&d_ext=html
// 1.3.1. Папки преобразуются в файлы html, без индексных, а php и htm также конвертируются в html
//        ?d_conv=1&d_idx=&d_ext=html&c_from=php:htm&c_to=html
// 2.1.3. В папки преобразуются только файлы php, только индексные
//        ?f_conv=&f_idx=1&f_ext=php
// 2.2.2. В папки преобразуются только файлы html, без учета индексных
//        ?f_conv=1&f_idx=&f_ext=html
// 2.3.1. В папки преобразуются файлы html (а php и htm также конвертируются в html), с учетом индексных
//        ?f_conv=1&f_idx=1&f_ext=html&c_from=php:htm&c_to=html

// условия обработки
// нужно проводить исключительно с массивом
// в конце будет обновлен при помощи $uri -> setFromArray()
// для экстренного обновления также можно использовать $uri -> setFromArray()

// конвертация

if (
	$convert_to &&
	$convert_from &&
	$file &&
	Objects::match($convert_from, $file['extension'])
) {
	Objects::relast($route, $file['name'] . '.' . $convert_to);
	$uri -> setFromArray();
}

// захват ошибки
// php файлы запрещены как системные
// за исключением тех случаев, когда их использование разрешено настройками роутинга

if ($file && $file['extension'] === 'php') {
	// обнаружили файл с расширением php
	
	// в прошлый раз мы делали запрещение ошибки по-умолчанию
	// и смотрели условия, когда она будет разрешена
	// однако проще сделать наоборот
	// разрешить ошибку и смотреть условия ее запрещения
	// таким образом, мы сократили число проверок и убрали вложенные проверки
	
	// триггер ошибки
	$err = true;
	
	if (Objects::match($files_extension, 'php')) {
		// есть массив расширений, с которыми будет проводится обработка,
		// если php там не указан, то смысл проверять дальше
		// сразу выводим ошибку
		// в противном случае, рассматриваем все подробно
		
		if ($files_convert && $files_index) {
			// обработка файлов, в том числе индексных, разрешена
			// это значит, что все они будут преобразованы
			// и ошибку выводить не нужно
			$err = null;
		} elseif ($files_convert && !$files_index && $file['name'] !== $index_file) {
			// обработка файлов разрешена,
			// но запрещена обработка индексных файлов
			// а если файл не индексный, он будет обработан и ошибку выводить не нужно
			$err = null;
		} elseif (!$files_convert && $files_index && $file['name'] === $index_file) {
			// обработка файлов все еще запрещена
			// но теперь разрешена обработка индексных файлов
			// это значит, что нужно проверить, индексный ли это файл
			// и если файл индексный, он будет обработан и ошибку выводить не нужно
			$err = null;
		}
		
	}
	
	if ($folders_convert && $folders_extension === 'php') {
		// если мы преобразовываем папки в файлы, то эти файлы тоже являются годными
		// но только в том случае, если мы преобразовываем в php
		
		if ($file['name'] !== $index_file) {
			// если наш файл не индексный
			// все в порядке, ошибок здесь нет
			$err = null;
		} elseif ($folders_index && $file['name'] === $index_file) {
			// если же файл индексный
			// и при этом обработка папок идет как раз в индексный файл
			// тоже все в порядке, тоже нет ошибки
			$err = null;
		}
		
	}
	
	// во всех остальных случаях нужно выводить ошибку
	
	if ($err) {
		// здесь не должно быть перенаправления на страницу ошибки
		// здесь нужно задать код и вывести состояние ошибки, не делая редирект
		// чтобы клиент понимал, что код принадлежит текущей странице
		// иначе получится так, что текущая страница перенаправляет на страницу ошибки
		$state -> set('error', 404);
	}
	
}

// условия обработки
// нужно проводить исключительно с массивом

if ($folders_convert) {
	
	if (!$file) {
		// если нет файла, т.е. наша цель - папка и при этом есть настройки преобразования папки в файл
		
		if ($folders_index) {
			// если задано преобразование в индексный файл,
			// то индексный файл добавляется в роутинг
			//$route[] = 'index.' . $folders_extension;
			$uri -> addPathArray('index.' . $folders_extension);
		} else {
			// если нет, то последний элемент роутинга преобразуется в файл с заданным расширением
			$last = Objects::last($route, 'value');
			Objects::relast($route, $last . '.' . $folders_extension);
			unset($last);
		}
		
	} elseif (
		!$folders_index &&
		$file['name'] === $index_file &&
		$file['extension'] === $folders_extension
	) {
		// отдельный случай для индексных файлов, когда наша цель - папка
		// но при этом есть индексный файл, а преобразование в индексный файл не задано
		// здесь имеет смысл убрать индексный файл
		$route = Objects::unlast($route);
	}
	
} elseif (
	$file &&
	($files_convert || $files_index) &&
	Objects::match($files_extension, $file['extension'])
) {
	// если наша цель - файл
	// его расширение входит в список допустимых к обработке
	// и при этом есть настройки преобразования файла в папку
	
	if (
		$files_index &&
		$file['name'] === $index_file
	) {
		// если задано преобразование индексных файлов
		// и текущий файл является индексным
		// просто убираем его из роутинга
		$route = Objects::unlast($route);
	}
	
	if (
		$files_convert &&
		$file['name'] !== $index_file
	) {
		// если задано преобразование файлов как в принципе
		// и текущий файл не является индексным
		// меняем его в роутинге на запись без расширения
		Objects::relast($route, $file['name']);
	}
	
}

// финальная стадия - апдейтим все данные урл из получившегося массива

$uri -> setFromArray();

/*
echo '<br>path : '   . str_replace(['Array', '[base] => '], null, print_r($uri -> path, 1));
echo '<br>file : '   . str_replace('Array', null, print_r($uri -> file, 1));
echo '<br>' . ($uri -> folder ? '<span style="color: green">' : null) . 'folder : '  . str_replace('Array', null, print_r($uri -> folder, 1)) . ($uri -> folder ? '</span>' : null);
echo '<br>ori : '    . str_replace('Array', null, print_r($uri -> original, 1));
echo '<br>url : '    . str_replace('Array', null, print_r($uri -> url, 1));
echo '<br>' . ($uri -> reload ? '<span style="color: red">' : null) . 'reload : ' . str_replace('Array', null, print_r($uri -> reload, 1)) . ($uri -> reload ? '</span>' : null);

exit;
*/

?>