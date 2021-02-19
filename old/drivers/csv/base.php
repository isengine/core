<?php

function dbSelect($db, $name) {
	
	// Упрощенная функция чтения определенных настроек или параметров
	// На входе указать раздел базы данных и какой именно параметр раздела интересует
	// На выходе формирует правильный запрос с подключением к базе данных и возвращает значения
	// 
	// При запуске делает предварительную обработку параметров
	// В конце, после обращения в базу данных, но перед выводом результатов, также может делать обработку данных
	
	$base = [
		'structures' => [
			'name' => PATH_DATABASE . DS . $name . '.csv',
			'json' => true,
			'format' => 'structure'
		],
		'settings' => [
			'name' => PATH_DATABASE . DS . $name . '.csv',
			'json' => true,
			'format' => false
		],
		'templates' => [
			'name' => PATH_TEMPLATES . DS . $name . DS . 'settings.csv',
			'json' => true,
			'format' => false
		]
	];
	
	return dbUse($db, 'select', false, $base[$db]['name'], ['limit' => 1, 'json' => $base[$db]['json'], 'format' => $base[$db]['format']]);
	
}

function dbLang($type, $templatename = false, $templatelang = false) {
	
	// Упрощенная функция чтения языковых параметров
	// На входе указать тип объекта - languages, dictionary
	// 
	// type: languages / common / dictionary
	// templatelang: $template -> lang
	// templatename: $template -> name / 'base'
	// 
	// На выходе формирует правильный запрос с подключением к базе данных и возвращает значения
	// 
	// При запуске делает предварительную обработку параметров
	// В конце, после обращения в базу данных, но перед выводом результатов, также может делать обработку данных
	
	if (
		!$templatename ||
		!$templatelang
	) {
		global $template;
	}
	
	$base = [
		'languages' => [
			'name' => 'lang',
			'format' => false
		],
		'lng' => [
			'name' => 'lang',
			'format' => false
		],
		'common' => [
			'name' => 'common',
			'format' => false
		],
		'dictionary' => [
			'name' => 'dictionary',
			'format' => true
		],
		'dic' => [
			'name' => 'dictionary',
			'format' => true
		]
	];
	
	return dbUse(
		'languages',
		'select',
		$base[$type]['name'],
		[
			'name' => ($templatelang) ? $templatelang : $template -> lang,
			'template' => ($templatename) ? $templatename : $template -> name
		],
		[
			'limit' => 1,
			'json' => true,
			'format' => $base[$type]['format']
		]
	);
	
}



function dbUse($db, $query, $array = false, $where = false, $options = false) {
	
	// Здесь мы прописываем драйвер, который будет работать непосредственно с массивом данных - читать из него, писать в него и т.п.
	// При запросе возвращает значения или статус (напр. для записи и удаления)
	
	// сначала устанавливаем базовые параметры
	// 1. $db - с каким разделом базы данных работаем
	// 2. $query - с каким запросом работаем, т.е. что делаем
	// 3. $array - с какими колонками работаем
	//    например, если запрос на чтение и этот параметр пропущен, то подразумевается что работаем со всеми колонками
	//    если в том же случае мы укажем неассоциированный массив, то значит нам нужна выборка только этих колонок
	//    или есть здесь строка, нас интересует только одна эта колонка
	// 4. $where - данные для проверки, поиска и выборки строк
	//    это ассоциированный массив, в котором ключ соответствует названию колонки, а значение - шаблону поиска
	//    другими словами, нам будут выданы только те строки, в заданных колонках которых есть указанное значение
	// 5. $options - дополнительные опции
	//    сюда входят настройки обработки запроса
	//    например, лимит выдаваемых строк, сортировка строк, являются ли данные в формате json и как их обрабатывать
	
	// список опций:
	// order - настройки сортировки
	// limit - лимит, пока лимит работает true/false на вывод одной строки
	// first - считать только одну строку, true/false, отличие от limit 1 в том, что first выводит строку без ключей
	// json - данные в формате json, для локалки это устанавливает другой обработчик, который читает файлы ini и разбирает их, для бд это будет означать, что данные хранятся в поле data в формате json
	// format - надстройка к json, true - разобрать данные как массив, false - как объект
	// add - просто массив дополнительных настроек, сейчас используется для модулей для установки папки и расширения
	// crypt - передаются ли данные в зашифрованном виде, сейчас используется для verify
	// keys - установить в качестве ключей массива данных указанное здесь поле
	
	// важно учесть, что если функция отдает на выходе false, это может говорить о том,
	// что она была вызвана с неправильными параметрами
	// соответственно, можно обработать вызов, например, так:
	// if (!dbUse(...)) { echo 'Error'; }
	
	if (
		empty($db) ||
		!is_string($db) ||
		!$query ||
		!in_array($query, ['select', 'count', 'insert', 'update', 'delete', 'verify', 'connect']) ||
		(
			in_array($query, ['insert', 'update', 'delete']) &&
			(!defined('isWRITE') || !isWRITE)
		)
	) {
		return false;
	}
	
	if (is_bool($array) || !$array) {
		$array = [];
	} elseif (is_string($array) || is_numeric($array)) {
		$array = [$array];
	}
	
	if (is_bool($where) || !$where) {
		$where = [];
	} elseif (is_string($where) || is_numeric($where)) {
		$where = ['name' => $where]; // упрощает запись для вывода только по главной колонке идентификации, в любом кстати разделе - name
	}
	
	if (is_object($options)) {
		$options = (array) $options;
	}
	
	// задаем объект, содержащий все необходимые данные, с которым и будем работать
	
	$db = (object) [
		'name' => $db,
		'query' => (string) $query,
		'array' => (array) $array,
		'where' => (array) $where,
		'options' => [
			'order' => (isset($options['order'])) ? $options['order'] : false,
			'limit' => (isset($options['limit'])) ? $options['limit'] : false,
			'first' => (isset($options['first'])) ? $options['first'] : false,
			'json' => (isset($options['json'])) ? $options['json'] : false,
			'format' => (isset($options['format'])) ? $options['format'] : false,
			'add' => (isset($options['add'])) ? $options['add'] : false,
			'crypt' => (isset($options['crypt'])) ? $options['crypt'] : false,
			'key' => (isset($options['key'])) ? $options['key'] : false,
		],
		'var' => array(),
		'result' => array()
	];
	
	unset($query, $array, $where, $options);
	
	// несколько специализированных запросов,
	// которые рекурсивно вызывают данную функцию
	
	// запрос проверки по крипте
	
	if ($db -> query === 'verify') {
		
		if ($db -> options['crypt']) {
			
			// если текущий объект с криптой, то перебираем все значения таблицы,
			// последовательно декодируем их и сверяем с исходными данными
			// другой вариант - сохранять в таблице доп.ячейку с хэшем по md5 или crypt с солью
			// и затем сравнивать не значения, а хэш с хэшем
			// но этот вариант ЗДЕСЬ не реализован
			// сейчас он сделан отдельно в процессоре регистрации
			
			$db -> result = dbUse($db -> name, 'select', $db -> array[0]);
			if (
				is_array($db -> result) &&
				!empty($db -> result)
			) {
				foreach ($db -> result as $k => $i) {
					if (crypting($i[$db -> array[0]], 1) !== crypting($db -> where['name'], 1)) {
						unset($db -> result[$k]);
					}
				}
			}
			
		} else {
			
			// если текущий объект не закриптован, то просто пробуем читать из таблицы
			// строку с таким же значением нужной ячейки
			
			$db -> result = dbUse($db -> name, 'select', $db -> array[0], [$db -> array[0] => $db -> where['name']]);
			
		}
		
		if (
			is_array($db -> result) &&
			count($db -> result)
		) {
			$db -> result = true;
		} else {
			$db -> result = false;
		}
		
	}
	
	// далее идет обработка
	// стандартных запросов
	
	// дополнительная настройка в зависимости от $db -> name и, иногда, от $db -> array
	
	if ($db -> name === 'languages') {
		$db -> where['name'] = PATH_TEMPLATES . DS . $db -> where['template'] . DS . 'lang' . DS . $db -> where['name'];
		if ($db -> array[0] === 'lang') {
			$db -> where['name'] .= '.lng';
		} elseif ($db -> array[0] === 'common') {
			$db -> where['name'] .= '_common.lng';
		} elseif ($db -> array[0] === 'custom') {
			$db -> where['name'] .= '_custom.lng';
		} elseif ($db -> array[0] === 'dictionary') {
			$db -> where['name'] .= '.dic';
		}
	} elseif ($db -> name === 'modules') {
		$db -> where['name'] = PATH_MODULES . DS . $db -> where['name'] . DS . 'data' . DS . ($db -> where['param'] ? $db -> where['param'] : 'settings') . '.ini';
	} elseif ($db -> options['add']['folder'] && $db -> name === 'articles_' . $db -> options['add']['folder']) {
		$db -> where['name'] = PATH_CONTENT . DS . $db -> options['add']['folder'] . DS . $db -> where['name'] . '.' . $db -> options['add']['ext'];
	}
	
	
	
	if ($db -> options['json']) {
		
		// работа с файлом в формате json
		
		$db -> result = dataloadjson($db -> where['name'], $db -> options['format']);
		
	} else {
		
		// работа с файлом в формате csv
		
		$db -> name = PATH_BASE . DS . $db -> name . '.csv';
		
		if (!file_exists($db -> name) || filesize($db -> name) === 0) {
			$db -> result = false;
		}
		
		/*
		if     ($db -> query === 'select' || $db -> query === 'count')  { $db -> var['stream'] = 'r'; }
		elseif ($db -> query === 'update' || $db -> query === 'delete') { $db -> var['stream'] = 'r+'; }
		elseif ($db -> query === 'insert')                              { $db -> var['stream'] = 'a'; }
		$handle = fopen($db -> name, $db -> var['stream'] . 'b');
		*/
		
		// обработка csv
		
		if ($db -> query === 'select') {
			
			// чтение таблицы
			
			$handle = fopen($db -> name, 'rb');
			$keys = str_getcsv(fgets($handle));
			
			while ($file = fgetcsv($handle)) {
				
				if (count($file) !== count($keys)) {
					break;
				}
				
				$file = array_combine($keys, $file);
				
				if (!empty($db -> where) && $db -> options['crypt']) {
					foreach(array_flip($db -> where) as $item) {
						$file[$item] = crypting($file[$item], 1);
					}
					if (array_intersect_assoc($file, $db -> where)) {
						$db -> var['buffer'] = $file;
					}
				} elseif (count($db -> where)) {
					if (array_intersect_assoc($file, $db -> where)) {
						$db -> var['buffer'] = $file;
					}
				} else {
					$db -> var['buffer'] = $file;
				}
				
				if (
					count($db -> array) &&
					is_array($db -> var['buffer']) &&
					count($db -> var['buffer'])
				) {
					$db -> var['buffer'] = array_intersect_key($db -> var['buffer'], array_flip($db -> array));
				}
				
				if ($db -> var['buffer']) {
					if (
						$db -> options['key'] &&
						!empty($db -> var['buffer'][$db -> options['key']])
					) {
						$db -> result[$db -> var['buffer'][$db -> options['key']]] = $db -> var['buffer'];
					} else {
						$db -> result[] = $db -> var['buffer'];
					}
				}
				
				$db -> var['buffer'] = '';
				
				if (
					$db -> options['limit'] &&
					count($db -> result) >= $db -> options['limit']
				) {
					break;
				}
				
				if ($db -> options['first'] && is_array($db -> result) && !empty($db -> result)) {
					$db -> result = array_shift($db -> result);
					break;
				}
				
			}
			
			fclose($handle);
			
		} elseif ($db -> query === 'connect') {
			
			// проверка таблицы
			
			if (file_exists($db -> name)) {
				
				$handle = fopen($db -> name, 'rb');
				$keys = str_getcsv(fgets($handle));
				
				if (count($keys) && !empty($keys[0])) {
					$db -> result = true;
				} else {
					$db -> result = false;
				}
				
				fclose($handle);
				
			} else {
				$db -> result = false;
			}
			
		} elseif ($db -> query === 'count') {
			
			// чтение таблицы
			$handle = fopen($db -> name, 'rb');
			$keys = str_getcsv(fgets($handle));
			$db -> result = 0;
			
			while ($file = fgetcsv($handle)) {
				
				if (count($file) !== count($keys)) {
					break;
				}
				
				$file = array_combine($keys, $file);
				
				if (count($db -> where)) {
					
					if (in_array('not null', $db -> where)) {
						if (array_diff(array_intersect_key($file, $db -> where), array(''))) {
							$db -> result++;
						}
					} else {
						if (array_intersect_assoc($file, $db -> where)) {
							$db -> result++;
						}
					}
					
				} else {
					$db -> result++;
				}
				
				if ($db -> options['limit']) {
					break;
				}
				
			}
			
			fclose($handle);
			
		} elseif ($db -> query === 'insert') {
			
			$handle = fopen($db -> name, 'rb');
			$keys = str_getcsv(fgets($handle));
			fclose($handle);
			
			//print_r($keys);
			//echo '<br>';
			
			//print_r($db -> array);
			//echo '<br>';
			
			if (objectKeys($db -> array)) {
				//добавить ассоциативный массив
				$db -> array = array_merge(array_fill_keys($keys, null), $db -> array);
			} else {
				//добавить неассоциативный массив, где только индексы
				//$db -> var['len'] = count($keys) - count($db -> array);
				//$db -> var['nullarr'] = array_fill(0, $db -> var['len'], null);
				//$db -> array = array_merge($db -> array, $db -> var['nullarr']);
				$db -> array = array_merge($db -> array, array_fill(0, count($keys) - count($db -> array), null));
			}
			
			//print_r($db -> array);
			//echo '<br>';
			
			$handle = fopen($db -> name, 'ab');
			fputcsv($handle, $db -> array);
			fclose($handle);
			
		}
		
		// добавить insert потоком, например massinsert когда $db -> array - индексный массив, каждый из которых как отдельный $db -> array
		// делается это для того, чтобы выполнить задачу в одном потоке и одном подключении, а не мучить систему постоянными подключить/отключить
		
		// добавить сравнение > < >= <= = != для select и count
		
		// добавить проверку соединения connect или обработчик ошибок, т.к. базы могут быть разные
		
		//print_r($db -> result);
		//exit;
		
		//$db -> result = dataloadcsv($db -> name, (object) ['keys' => true]);
	}
	
	// возвращаем данные
	
	return $db -> result;
	
}

?>