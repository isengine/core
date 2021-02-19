<?php defined('isENGINE') or die;

if (defined('isPROCESS')) {
	error('403', false, 'is hack attempt to change system constant isPROCESS');
}

// инициализируем процессор, если был запрос
// а также проверка прав пользователя на действия с той или иной таблицей базы данных:
// поле, строка, раздел
// доступ на запись, изменение, добавление, удаление и даже на чтение, чтобы чего лишнего не прочесть
// и кроме того, еще плюс при изменении базы данных
// переподключение к базе под новым пользователем, которому разрешена запись (isWRITING),
// но это только для тех случаев, когда заданы SECURE_WRITING, DB_WRITINGUSER (defined) и DB_WRITINGPASS (defined)

global $uri;

if (
	!empty($uri -> path -> array) && reset($uri -> path -> array) === DEFAULT_PROCESSOR
	//isset($_POST['query']) || isset($_GET['query']) // старое условие
) {
	
	define('isPROCESS', true);
	
	// готовим параметры процесса
	
	if (strtolower($_SERVER['REQUEST_METHOD']) === 'post') {
		$temp = $_POST;
	} else {
		$temp = $_GET;
	}
	
	$path = $uri -> path -> array;
	$path = array_slice($path, 1, 3);
	$path = array_pad($path, 6, null);
	$path = array_combine(['parent', 'name', 'status', 'type', 'path', 'vendor'], $path);
	
	global $process;
	
	$process = (object) [
		'method' => strtolower($_SERVER['REQUEST_METHOD']),
		'set' => (object) $path,
		/*
		'type' => set($uri -> path -> array[1], clear($uri -> path -> array[1], 'simpleurl')),
		'parent' => set($uri -> path -> array[2], clear($uri -> path -> array[2], 'simpleurl')), // бывший 'param'
		'name' => set($uri -> path -> array[3], clear($uri -> path -> array[3], 'simpleurl')),
		'status' => set($uri -> path -> array[4], clear($uri -> path -> array[4], 'simpleurl')),
		'path', // бывший 'open'
		*/
		'hash' => (int) set($temp['hash'], crypting(clear($temp['hash']), true)),
		'csrf' => set($temp['csrf'], clear($temp['csrf'])),
		'check' => isset($temp['check']) && !set($temp['check']) ? true : false,
		'time' => time(),
		'data' => !empty($temp['data']) ? $temp['data'] : null,
		'source' => !empty($temp['source']) ? $temp['source'] : null,
		'close' => !empty($temp['close']),
		'errors' => [],
		'var' => []
	];
	
	$pp = dataSplit($process -> set -> parent, '.');
	$process -> set -> parent = $pp[0];
	$process -> set -> vendor = !empty($pp[1]) ? $pp[1] : 'isengine';
	
	unset($temp, $path, $pp);
	
	if (
		empty($process -> set -> parent) ||
		empty($process -> set -> name)
	) {
		error('403', true, 'process was called without name or parent');
	} else {
		
		$processdb = dbUse('processor:' . $process -> set -> name, 'select', ['allow' => 'parent:' . $process -> set -> parent . ($process -> set -> vendor !== 'isengine' ? ' type:module.' . $process -> set -> vendor : null), 'return' => 'alone']);
		
		//$processdb = dbUse('processor:' . $process -> set -> name, 'select', ['allow' => 'parent:' . $process -> set -> parent, 'return' => 'alone']);
		//print_r($processdb);
		//exit;
		
		if (empty($processdb)) {
			error('403', true, 'process not registered in database or user not allow to it');
		} else {
			$process -> set -> type = $processdb['type'];
			$process -> set -> secure = set($processdb['data']) ? $processdb['data'] : null;
		}
		
		unset($processdb);
		
		/*
		if (objectIs($process -> set -> secure)) {
			init('processor', 'secure');
			if (!empty($process -> set -> secure)) {
				init('processor', 'second');
				dbUse('attempts', 'write', [$process -> set -> secure]);
			}
		} else {
			init('processor', 'second');
		}
		*/
		
		if (objectIs($process -> set -> secure)) {
			init('processor', 'secure');
			if (empty($process -> set -> secure)) {
				error('403', true, 'process was closed on secure checking');
			}
		}
		init('processor', 'second');
		
		if ($process -> close) {
			
			// закрываем процесс, если это необходимо
			exit;
			
		} else {
			
			// переинициализируем драйвера обратно только на чтение из базы данных
			//init('drivers', 'third');
			
			// это раньше нужно было переинициализировать драйвера
			// сейчас в этом нет необходимости, т.к. процесс завершается в любом случае
			// и если вы хотите продолжить загрузку, будет ошибка, т.к. процесс будет разбираться как несуществующая страница или раздел
			// причина этому в том, что раньше процессы грузились через запросы, а теперь - по урлу
			// поэтому в данном случае если нужно продолжать, то после завершения процесса будет заново загружена страница,
			// но при этом нужные параметры передадутся в нее (например, для определения неправильно заполненных полей)
			
			// в связи с этим также была убрана константа isREAD из условий в драйвере базы данных
			
			// это хорошо с точки зрения логики страниц и для ajax-запросов
			// однако процессы, которые продолжают выполнение, нехороши с точки зрения повторной загрузки страницы, всяких повторных инициализаций и прочего
			
			reload(
				$uri -> site . $uri -> previous . $uri -> query -> string,
				null,
				['Content-Type' => 'text/html; charset=UTF-8']
			);
			//header('Content-Type: text/html; charset=UTF-8');
			//header('Location: ' . $uri -> site . $uri -> previous . $uri -> query -> string);
			//exit;
			
		}
		
	}
	
} else {
	define('isPROCESS', false);
}

?>