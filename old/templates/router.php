<?php defined('isENGINE') or die;

global $template;
global $uri;
	
// ВПОЛНЕ ВОЗМОЖНО, ЧТО ЗДЕСЬ МНОГО НЕНУЖНЫХ ПАРАМЕТРОВ,
// В ЧАСТНОСТИ В $template -> list И В $template -> path
// ПОТОМУ ЧТО ОНИ МОГУТ ДУБЛИРОВАТЬСЯ С УЖЕ СУЩЕСТВУЮЩИМИ

// ТАКЖЕ НУЖНО БЫТЬ ОЧЕНЬ АККУРАТНЫМИ И ВНИМАТЕЛЬНЫМИ С ПАРАМЕТРАМИ $template -> page['parents']
// ПОТОМУ ЧТО НАПРИМЕР $template -> path -> array (убран, нет его!) ДОНАСТРАИВАЕТСЯ В ИНИЦИАЛИЗАЦИИ КОНТЕНТА

// узнаем параметры шаблона
// из структуры и запрошенной страницы

$template -> page = structureSearch($template -> list -> router, $template -> list -> structure);

//echo '<br>URI:<br><pre>' . print_r($uri, 1) . '</pre>';
//echo '<br>LIST:<br><pre>' . print_r($template -> list, 1) . '</pre>';
//echo '<br>PAGE:<br><pre>' . print_r($template -> page, 1) . '</pre>';

// проверяем домашнюю страницу на отсутствие в урле папок

// условие ниже появилось из-за того, что в структуре не был проставлен тип для домашней папки
/*
if ($template -> name === 'home') {
	logging('error from router -- redirect to home page');
	header($_SERVER['SERVER_PROTOCOL'] . ' 301 Moved Permanently', true, 301);
	header('Location: ' . $uri -> site);
} else
*/
$home = null;

if (
	empty($template -> page) &&
	!empty($template -> list -> router) &&
	!empty($template -> list -> folders) &&
	!in_array(end($template -> list -> router), $template -> list -> folders)
) {
	// условие, которое позволяет выдать ошибку без обработки
	// надо тестировать
	error('404', true, 'error 404 from router -- no page in structure');
} elseif ($template -> page['type'] === 'home' && !empty($uri -> path -> array)) {
	$home = $template -> page['home'];
	unset($template -> page);
} elseif (empty($template -> page['home'])) {
	$home = structureSearch(null, $template -> list -> structure);
	$template -> page['home'] = $home = objectIs($home) ? $home['home'] : null;
}

// убираем список параметров из объекта шаблон и помещаем их в объект uri,
// а также в uri чистим от них пути, а их перемещаем в раздел запросов

if (!empty($template -> page['parameters'])) {
	
	$c = count($uri -> path -> array) - count($template -> page['parameters']);
	
	$uri -> query -> path = $template -> page['parameters'];
	
	if ($c > 0) {
		array_splice($uri -> path -> array, $c);
	} else {
		$uri -> path -> array = [];
	}
	
	$uri -> path -> string = !empty($uri -> path -> array) ? objectToString($uri -> path -> array, '/') . '/' : '';
	
	unset($c);
	
}

unset($template -> page['parameters']);

// если страницы нет, то выводим ошибку

//echo '<br>PAGE:<br><pre>' . print_r($template -> page, 1) . '</pre>';

if (empty($template -> page)) {
	if (!in_array($template -> name, $template -> list -> folders)) {
		error('404', true, 'error 404 from router -- not found template');
	} elseif (count($template -> list -> router) > 1) {
		logging('error from router -- redirect to main page');
		reload($uri -> site . $template -> name . '/');
		//header('Location: ' . $uri -> site . $template -> name . '/');
	}
}

// настраиваем параметры страницы

if (!empty($template -> page['parent']['array'])) {
	$template -> list -> router = $template -> page['parent']['array'];
	if (!empty($template -> page['parent']['template'])) {
		array_unshift($template -> list -> router, $template -> page['parent']['template']);
	}
}

if (!empty($template -> page['parent']['folders'])) {
	array_pop($template -> page['parent']['folders']);
	$template -> page['parents'] = $template -> page['parent']['folders'];
}

// вычисляем текущий шаблон

if (!empty($template -> page['parent']['template'])) {
	
	$template -> name = $template -> page['parent']['template'];
	
	if (in_array($template -> name, $template -> list -> folders)) {
		
		$template -> path -> init .= $template -> name . DS;
		//$template -> path -> url .= $template -> name . '/';
		
		array_shift($template -> list -> router);
		
	} else {
		
		//error('403', false, 'needed template was not found');
		
	}
	
} else {
	
	//print_r($template);
	//print_r($template -> page);
	
	if (in_array($template -> name, $template -> list -> folders)) {
		
		$template -> path -> init .= $template -> name . DS;
		//$template -> path -> url .= $template -> name . '/';
		
		//if (!in_array($template -> name, $template -> list -> structure)) {
		if (!objectIs(structureSearch([$template -> name], $template -> list -> structure))) {
			if (file_exists($template -> path -> init . 'section' . DS)) {
				
				$template -> section = true;
				$template -> path -> init .= 'section' . DS;
				
				//$template -> page['name'] = 'home';
				// условие было изменено, и теперь его нужно тестировать
				$template -> page['name'] = !empty($home) ? $home : $template -> page['home'];
				
				//$template -> name = 'default';
				//$template -> path -> url .= 'section/default/';
				
			} else {
				error('404', true, 'error 404 from router -- not found section');
			}
		}
		
		// строки выше были изменены уже позже, намного позже разработки роутера и проведения различных тестов
		// в изменении появилась необходимость из-за возникновения ситуаций, которых раньше не было
		// дело в том, что раньше в системе предусматривался только один шаблон админки
		// но т.к. реализация шаблона админки по-сути ничем не отличается от любого другого шаблона закрытого раздела,
		// эта позиция была пересмотрена, в частности, из кода были убраны все константы и условия проверки именно админки
		// теперь новая задача - реализовать админку на имеющихся мощностях системы,
		// доработки разрешены, но только при условии, что они будут универсальные для всех шаблонов
		
		// итак, мы имеем шаблон, например, шаблон по-умолчанию, в структуре которого не указана админка
		// однако при ее запросе мы должны получить форму входа
		// как это реализовать?
		// нам на помощь приходит механизм секций
		// секции - это дополнения для вызова и отображения части опредеделенного шаблона из другого шаблона
		// например, если вы хотите видеть панель администратора при навигации по обычным, открытым, разделам сайта
		// или если вы из другого раздела сайта хотите перейти в шаблон админки
		// секции при этом должны включаться только если они есть в запрашиваемом шаблоне
		// согласно этим правилам мы и поменяли условия в роутере
		
		// в старом условии эта проверка непроходима для parent.template
		// потому что отсутствие принудительного шаблона в списке шаблонов или структур
		// будет выводить ошибку при проверке ранее
		// как это отразится в новом, доработанном роутере, неизвестно
		
		array_shift($template -> list -> router);
		
	} else {
		
		$template -> name = 'default';
		$template -> path -> init .= 'default' . DS;
		//$template -> path -> url .= 'default/';
		
	}
	
}

if (empty($template -> list -> router)) {
	$template -> page['name'] = !empty($home) ? $home : $template -> page['home'];
	$template -> list -> router[] = $template -> page['home'];
}

// настраиваем пути текущего шаблона

$template -> path -> page = $template -> path -> init . ($template -> section ? 'init' : 'inner' . DS . objectToString($template -> list -> router, DS)) . '.php';

unset($template -> page['parent'], $template -> list, $home);

// проверяем существование страницы безо всяких параметров

if (
	empty($template -> page['type']) &&
	!file_exists($template -> path -> page)
) {
	error('404', true, 'error 404 from router -- not found page');
}

//print_r($template);

//echo '<br>URI:<br><pre>' . print_r($uri, 1) . '</pre>';
//echo '<br>PAGE:<br><pre>' . print_r($template -> page, 1) . '</pre>';

?>