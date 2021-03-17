<?php defined('isENGINE') or die;

function structureRemap($arrTarget, $parent = null, $c = null, $level = null) {
	
	// 
	// Рекурсивная функция, перебирает массив любой глубины
	// 
	// Задача функции - переименовать ключи структуры вида '0' в вид 'parent:0'
	// 
	// функция перебирает значения и ключи внутри массива arrTarget
	// к каждому ключу, если он не является элементом первого уровня, добавляется его родитель
	// если ключ является числовым значением, то к нему добавляется сравивает их с содержимым массива
	// 
	// в результате возвращает готовый переработанный массив arrTarget
	// 
	
	static $c;
	static $level;
	if (!$c) { $c = 0; }
	if (!$level) { $level = 1; }
	
	foreach ($arrTarget as $key => $item) {
		
		// так как у нас структура меню может содержать типы, указываемые через знак ':',
		// то создаем копии названий ключей и запись подпунктов делаем по ним
		
		$data = [
			'id' => null,
			'name' => null,
			'type' => null,
			'parent' => $parent,
			'data' => null,
			'base' => null,
			'template' => null,
			'level' => $level
			// base задает символ вначале
			// раньше была точка '.', которая преобразовывалась в слеш в начале ссылки,
			// но теперь от нее отказались, т.к. не соответствует политике ссылок
		];
		
		$clear = dataParse($key);
		if (empty($clear)) {
			$clear = dataParse($item);
		}
		
		if (!empty($clear)) {
			
			$clear = objectFill($clear, [0, 1, 2, 3]);
			
			if (is_numeric($clear[0])) {
				$data['id'] = array_shift($clear);
			} else {
				$data['id'] = $c;
			}
			
			$data['name'] = $clear[0];
			$data['type'] = $clear[1];
			$data['data'] = $clear[2];
			$data['template'] = $clear[3];
			
		}
		
		if (
			$data['parent'] &&
			$data['type'] !== 'url' &&
			$data['type'] !== 'int'
		) {
			$data['base'] .= $data['parent'];
		}
		
		// 'params' и 'content' проходят по-умолчанию, ссылка остается нормальной, при этом типы тоже остаются
		// дальше - в меню, и после роутинга - разбор идет уже по этим типам,
		
		
		switch ($data['type']) {
			case 'home'    : $data['data'] = ''; break;
			case 'group'   : $data['data'] = ''; break;
			case 'nolink'  : $data['data'] = ''; break;
			case 'none'    : $data['data'] = '#'; break;
			case 'hash'    : $data['data'] = $data['base'] . '#' . $data['name']; break;
			case 'action'  : $data['data'] = '#' . $data['name']; break;
			case 'url'     : $data['data'] = $data['data']; break;
			case 'int'     : $data['data'] = $data['base'] . $data['data'] . '.'; break;
			default        : $data['data'] = $data['base'] . $data['name'] . '.'; break;
		}
		
		//print_r($data);
		
		$name = $data['id'] . ':' . $data['name'] . ':' . $data['type'] . ':' . $data['data'] . ':' . $data['template'] . ':' . $level;
		$c++;
		
		if (is_array($item)) {
			if ($data['type'] === 'group') {
				$arrTarget[$name] = structureRemap($item, $data['parent'], $c, $level);
			} elseif ($data['parent']) {
				$arrTarget[$name] = structureRemap($item, $data['parent'] . $data['name'] . '.', $c, $level++);
				$level--;
			} else {
				$arrTarget[$name] = structureRemap($item, $data['name'] . '.', $c, $level++);
				$level--;
			}
		} else {
			$arrTarget[$name] = null;
		}
		unset($arrTarget[$key]);
		
	}
	
	unset($data, $key, $item);
	return $arrTarget;
	
}


function structureSearch($search, $structure, $parent = ['template' => null, 'array' => [], 'folders' => []]) {
	
	// 
	// Рекурсивная функция, перебирает массив любой глубины
	// 
	// Задача функции - найти в структуре указанные в массиве $search элементы
	// и вернуть данные последнего
	// 
	// массив для поиска указывается по порядку, например: ['folder', 'subfolder', 'element']
	// 
	// для правильной работы требуется подать на вход структуру без групп,
	// т.е. распакованную функцией structureUngroup
	// 
	// в результате возвращает либо данные найденного элемента,
	// либо пустой массив, если элемент в структуре не был найден или структура была задана неверно
	// 
	
	$result = [];
	// было так: $current = array_shift($search);
	$current = objectIs($search) ? array_shift($search) : null;
	
	$parent['folders'][] = $current;
	
	foreach ($structure as $key => $item) {
		
		$temp = dataParse($key);
		
		$data = [
			'id' => $temp[0],
			'name' => $temp[1],
			'type' => $temp[2],
			//'value' => $temp[3],
			'template' => $temp[4],
			'parameters' => $search,
			'parent' => [
				'template' => $parent['template'],
				'array' => $parent['array'],
				'folders' => $parent['folders']
			],
			'home' => null
		];
		
		// здесь идут доп условия
		
		// здесь мы устанавливаем родительский шаблон
		// родительский шаблон меняется только тогда, когда задан текущий шаблон
		// и при этом родительский шаблон не был задан
		// или он был задан ранее, но он НЕ такой же, как и текущий шаблон
		// иначе - шаблон не менялся
		
		// кроме того, если шаблон не менялся, то мы добавляем текущий элемент к общему пути
		// если же шаблон менялся, то мы сбрасываем текущий путь
		
		if (
			!empty($data['template']) &&
			(
				empty($data['parent']['template']) ||
				$data['parent']['template'] !== $data['template']
			)
		) {
			$data['parent']['template'] = $data['template'];
			//$data['parent']['array'][] = $data['name'];
			$data['parent']['array'] = [$data['name']];
		} else {
			$data['parent']['array'][] = $data['name'];
			//$data['parent']['template'] = null;
			//$data['parent']['array'] = [];
		}
		
		// здесь дополнительное условие для определения главной страницы
		
		if ($data['type'] === 'home') {
			$data['home'] = $data['name'];
			if (empty($current)) {
				$result = $data;
				break;
			}
		}
		
		// здесь идут нормальные условия
		
		if ($data['name'] === $current) {
			
			if (
				empty($search) ||
				$data['type'] === 'content' ||
				$data['type'] === 'params'
			) {
				$result = $data;
				break;
			} elseif (objectIs($item)) {
				$result = structureSearch($search, $item, $data['parent']);
				break;
			}
			
		}
		
	}
	
	return $result;
	
}

function structureUngroup($structure) {
	
	// 
	// Рекурсивная функция, перебирает массив любой глубины
	// 
	// Задача функции - избавить структуру от групп,
	// распаковав их
	// 
	// в результате возвращает готовый массив
	// 
	
	$result = [];
	
	foreach ($structure as $key => $item) {
		
		$data = dataParse($key);
		
		if ($data[2] === 'group') {
			
			if (is_array($item) && !empty($item)) {
				$addition = structureUngroup($item);
				$result = array_merge($result, $addition);
			}
			
		} elseif (is_array($item)) {
			$result[$key] = structureUngroup($item);
		} else {
			$result[$key] = $item;
		}
		
	}
	
	return $result;
	
}

?>