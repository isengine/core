<?php defined('isENGINE') or die;

use is\Functions\System;

global $user;
global $structure;

$structure = dbUse('structures', 'select', true);

foreach ($structure as $key => $item) {
	if ($key !== 'default') {
		$structure['default'] = array_merge($structure['default'], [$key => $item]);
	}
}
$structure = $structure['default'];

// загружаем функции
// ТЕПЕРЬ ФУНКЦИИ СТРУКТУРЫ ВЫДЕЛЕНЫ В ОТДЕЛЬНЫЙ ФАЙЛ
// ТАКЖЕ СТРУКТУРА ПОМЕНЯЛАСЬ ПОД :
// И ТЕПЕРЬ МОЖНО ЗАДАВАТЬ ID, ИМЯ, ТИП, РАЗНЫЕ ЗНАЧЕНИЯ (ЕСЛИ НУЖНО, НАПРИМЕР ДЛЯ URL)
// И... ВНИМАНИЕ... ТЕПЕРЬ ЕЩЕ И ШАБЛОН (!!!!!)
init('functions', 'structure');

// теперь пробегаем по структуре и переименовываем ключи

$structure = structureRemap($structure);

//print_r($structure);

?>