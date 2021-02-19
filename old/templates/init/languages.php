<?php defined('isENGINE') or die;

global $lang;
global $template;
global $dictionary;

$dictionary = [];

// загрузка языковых файлов
// здесь язык - предок файла, назначение - имя файла, а название шаблона - тип файла

// сюда еще нужно сделать проверку существования записей/разделов common dictionary morph
// во всех присутствующих в системе языковых пакетах
// и если таких записей нет, установить их из install

// загружаем стандартный языковой пакет

$db = dbUse(
	'languages:' . $lang -> lang . ':common:menu:custom:filter' . (in('options', 'dictionary') ? ':morph:dictionary' : null) . (in('options', 'translate') ? ':translate' : null),
	'select',
	['allow' => 'parent:' . $lang -> lang, 'deny' => 'type']
);

foreach ($db as $item) {
	
	if (empty($item['data'])) {
		continue;
	} elseif ($item['name'] === 'dictionary') {
		$dictionary = $item['data'];
	} elseif (
		$item['name'] === 'filter' ||
		$item['name'] === 'custom' ||
		$item['name'] === 'menu' ||
		$item['name'] === 'morph' ||
		$item['name'] === 'translate'
	) {
		$lang -> data[$item['name']] = $item['data'];
	} else {
		$lang -> data = array_merge(
			$lang -> data,
			$item['data']
		);
	}
	
}

unset($db);

// загружаем языковой пакет для текущего шаблона

$db = dbUse(
	'languages:' . $lang -> lang . ':common:menu:custom:filter' . (in('options', 'dictionary') ? ':morph:dictionary' : null) . (in('options', 'translate') ? ':translate' : null) . (objectGet('template', 'section') ? ':section' : null),
	'select',
	['allow' => 'parent:' . $lang -> lang . ' type:' . $template -> name]
);

if (!empty($db)) {
	
	foreach ($db as $item) {
		
		if (empty($item['data'])) {
			continue;
		} elseif ($item['name'] === 'dictionary') {
			$dictionary = array_merge(
				$dictionary,
				$item['data']
			);
		} elseif (
			$item['name'] === 'filter' ||
			$item['name'] === 'custom' ||
			$item['name'] === 'menu' ||
			$item['name'] === 'morph' ||
			$item['name'] === 'translate' ||
			$item['name'] === 'section'
		) {
			if (objectIs($lang -> data[$item['name']])) {
				$lang -> data[$item['name']] = array_merge(
					$lang -> data[$item['name']],
					$item['data']
				);
			} else {
				$lang -> data[$item['name']] = $item['data'];
			}
		} else {
			$lang -> data = array_merge(
				$lang -> data,
				$item['data']
			);
		}
		
	}
	
}

unset($db);

// обрабатываем переводчик

if (objectIs($lang -> data['translate'])) {
	
	foreach ($lang -> data['translate'] as &$item) {
		
		if (objectIs($item)) {
			
			$translate = [
				'origin' => [],
				'reverse' => [],
				'temp_origin' => [],
				'temp_reverse' => []
			];
			
			foreach ($item as $k => $i) {
				if (objectIs($i)) {
					foreach ($i as $ki => $ii) {
						
						// создаем копию оригинальной таблицы с дополнительными ключами длины слов
						$translate['temp_origin'][$ki][mb_strlen($k)][$k] = $ii;
						
						// создаем обратные копии, также по длине слов, причем для каждого языка - своя
						if (!empty($ii)) {
							$translate['temp_reverse'][$ki][mb_strlen($ii)][$ii] = $k;
						}
						
					}
					unset($ki, $ii);
				}
			}
			unset($k, $i);
			
			// сортируем копию по длине слов и сливаем в общий массив, с разделением для каждого языка
			foreach ($translate['temp_origin'] as $ki => $ii) {
				krsort($ii, SORT_NATURAL);
				$translate['origin'][$ki] = call_user_func_array('array_merge', $ii);
			}
			unset($ki, $ii);
			
			// сливаем обратные копии в общий массив, но для каждого языка
			foreach ($translate['temp_reverse'] as $ki => $ii) {
				krsort($ii, SORT_NATURAL);
				$translate['reverse'][$ki] = call_user_func_array('array_merge', $ii);
			}
			unset($ki, $ii);
			
		}
		
		unset($translate['temp_origin'], $translate['temp_reverse']);
		$item = $translate;
		
	}
	unset($item, $translate);
	
}

//echo '<pre>' . print_r($lang -> data['translate'], 1) . '</pre>';
//echo '<pre style="font-size: 10px; line-height: 6px;">';
//echo $template -> name . '<br>';
//echo count($db) . '<br>';
//print_r($db);
//print_r($lang);
//echo '</pre>';
//exit;


/*
// загрузка языковых файлов
// здесь язык - тип файла, остальное - имя файла

$db = dbUse(
	'languages:' . $lang -> lang . ':common:custom:menu' . (in('options', 'dictionary') ? ':morph:dictionary' : ''),
	'select',
	['allow' => 'type:' . $lang -> lang]
);

foreach ($db as $item) {
	
	if (empty($item['data'])) {
		continue;
	} elseif ($item['type'] === 'dictionary') {
		$lang -> dictionary = $item['data'];
	} elseif (
		$item['type'] === 'custom' ||
		$item['type'] === 'menu'
	) {
		$lang -> data[$item['type']] = $item['data'];
	} else {
		$lang -> data = array_merge(
			$lang -> data,
			$item['data']
		);
	}
	
}

echo '<pre style="font-size: 10px; line-height: 6px;">';
//print_r($db);
print_r($lang);
echo '</pre>';
exit;

// загрузка языковых файлов
// здесь язык - имя файла, остальное - тип файла

$db = dbUse('languages:' . $lang -> lang, 'select');

foreach ($db as $item) {
	
	if (empty($item['data'])) {
		continue;
	} elseif (
		$item['type'] === 'dictionary' && in('options', 'dictionary')
	) {
		$lang -> dictionary = $item['data'];
	} elseif (
		empty($item['type']) ||
		$item['type'] === 'common' ||
		$item['type'] === 'morph' && in('options', 'dictionary')
	) {
		$lang -> data = array_merge(
			$lang -> data,
			$item['data']
		);
	} else {
		$lang -> data[$item['type']] = $item['data'];
	}
	
}

echo '<pre style="font-size: 10px; line-height: 6px;">';
//print_r($db);
print_r($lang);
echo '</pre>';
exit;
*/

?>