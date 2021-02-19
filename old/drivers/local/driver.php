<?php defined('isENGINE') or die;

/* ОСНОВНЫЕ ФУНКЦИИ ПО РАБОТЕ С БАЗОЙ ДАННЫХ */

function dbUse($name, $query, $parameters = []) {
	
	// Здесь мы прописываем драйвер, который будет работать непосредственно с массивом данных - читать из него, писать в него и т.п.
	// При запросе возвращает значения или статус (напр. для записи и удаления)
	
	// ТЕПЕРЬ МОЖНО ИСПОЛЬЗОВАТЬ db:name:name не только в select, но и в write/delete/create
	// причем param можно указывать как раньше, массивом с массивами данных, так и сразу одним массивом данных
	// т.е. [0 => ['id' => '...', 'name' => '...', 'data' => '...']] или ['id' => '...', 'name' => '...', 'data' => '...']
	
	// сначала устанавливаем базовые параметры
	// $name - с каким разделом и с какими строками базы данных мы работаем
	// $query - с каким запросом работаем, т.е. что делаем
	// $parameters - параметры проверки, поиска, выборки и сортировки строк, а также представление возвращаемого массива данных
	
	// список запросов:
	// select - чтение и выборка данных из таблицы
	// filter - выборка данных из уже существующего массива данных, полученного чтением из таблицы
	//   * данный запрос хорош тем, что позволяет не обращаться лишний раз к базе данных,
	//   а фильтровать данные из уже полученного массива, при этом ничего в нем не меняя,
	//   а возвращая новый массив
	// count - подсчет определенных данных в массиве
	//   * ВПОЛНЕ ВОЗМОЖНО, ЭТОТ ЗАПРОС ПЕРЕЙДЕТ В ПАРАМЕТР ВОЗВРАЩАЕМЫХ ДАННЫХ
	// write - (раньше insert/update) - вставить новую или изменить существующую строку в таблице (защищенный запрос, поэтому работает только при включенной константе isWRITE)
	// delete - удалить строку из таблицы (защищенный запрос, поэтому работает только при включенной константе isWRITE)
	// verify - проверить определенные данные в таблице на определенное соответствие
	// connect - соединиться с базой данных, также с помощью этого запроса можно проверить состояние соединения и разорвать соединение
	
	// список параметров:
	// sort - производит сортировку массива, сортируется пока только по полям из блока data
	// filter - фильтр массива по полям из блока data
	// allow/deny - фильтр массива (разрешенные/запрещенные) по полям parent, type, self, (а также теперь и name) в будущем, возможно, также по ctime, mtime
	// or - сравнение фильтров: false или не указано - по-умолчанию, сравнение по 'и' / true - сравнение по 'или'
	// format - формат содержимого блока data: по-умолчанию json / structure / content
	// limit - лимит числа элементов в массиве: 0 или не указано - по-умолчанию, все элементы / любое число
	// skip - число пропускаемых элементов в массиве: 0 или не указано - по-умолчанию, нет пропуска / любое число
	//   * если задан skip, то limit считается от первого элемента без пропуска
	// return - определяет ключи массива: name / id / alone, можно указать второй параметр data - будет возвращен массив только с данными (name:data)
	//   * специальный ключ alone возвращает только одну (первую) найденную строку, он также работает со вторым параметром
	// если вы зададите вместо массива параметров false - тогда в условиях параметры будут считаться пустыми
	// вы также можете задать вместо массива параметров значение true - тогда будут браться параметры по-умолчанию
	
	// работа sort:
	// передается строка из двух аргументов, разделенных двоеточием - 'data:type:flag'
	// data - это ключ любого верхнего поля в массиве данных, по этому полю будет вестить сортировка
	//   * специальные значения 'id' и 'name' задают сортировку по полям 'id' или 'name' самого материала, а не внутри массива данных
	// type - это тип сортировки
	//   shuffle - перемешать в случайном порядке
	//   desc - в обратном порядке
	//   любое другое значение, в том числе если оно пропущено, сортирует массив в прямом порядке
	// flag - это флаг, определяющий метод
	//   regular - SORT_REGULAR, обычное сравнение элементов
	//   numeric - SORT_NUMERIC, числовое сравнение элементов
	//   string - SORT_LOCALE_STRING, сравнивает элементы как строки с учетом текущей локали
	//   * по-умолчанию сортировка производится с флагом SORT_NATURAL и SORT_FLAG_CASE
	//   * подробнее - см. https://www.php.net/manual/ru/function.sort.php
	
	// работа allow/deny:
	// практически как и работа filter
	// если указан allow, то выкидываются те материалы, которые не прошли проверку по 'и'
	// если указан deny, то фильтр срабатывает для всех материалов, которые прошли проверку по 'или'
	// поля для фильтра указываются через пробел, запятую или точку с запятой
	// их значения указываются через двоеточие
	// например: 'parent:first:second:third type:one:two:three self:username1:username2:username3'
	
	// работа filter:
	// по-умолчанию выборка идет по and, но вы можете установить тип выборки в параметре or - если не задан (по-умолчанию), 0 или false - тогда and, иначе - or
	// вы указываете сколько угодно значений через двоеточие и сколько угодно полей для выборки через пробел, запятую или точку с запятой
	// например такая строка
	//   'one:1:2 two:a:b:c'
	// будет разобрана в массив
	//   { 'one' : ['1', '2'], 'two' : ['a', 'b', 'c'] }
	// если же вы хотите задать выборку всех непустых полей (т.е. всех полей, кроме пустых, и неважно чем они заполнены),
	// используйте поле без значений
	//   'one two:a:b:c' или 'one: two:a:b:c'
	// если вы хотите использовать точное совпадение всех элементов, используйте для перебора вместо двоеточия ':' знак плюс '+'
	//   'one:1+2 two:a+b+c'
	// например, этот фильтр используется в работе контента для применения точного совпадения по родителям
	// если вы хотите, чтобы в поиске были выданы данные, у которых совпадает только один элемент из перечисленных, используйте вначале каждого значения знак плюс '+'
	//   'one:+1:+2 two:+a:+b:+c' или для одного значения 'one:+1'
	// если вы хотите, чтобы в поиске были выданы данные, у которых отсутствуют перечисленные элементы, используйте вначале каждого значения знак минус '-'
	//   'one:-1:-2 two:-a:-b:-c' или для одного значения 'one:-1'
	// если вы хотите использовать поиск строки, используйте оператор '*'
	//   'one:search*' или 'one:*search'
	// если вы хотите использовать поиск строки в массиве, используйте оператор '>'
	//   'one:search>' или 'one:>search'
	//   такая запись даст вам все строки, где one - массив и он содержит значение search с любым ключом
	// если вы хотите использовать поиск чисел в диапазоне, используйте оператор '_'
	//   'one:1_' - даст все значения, равные и больше 1
	//   'one:_10' - даст все значения, равные и меньше 10, в т.ч. отрицательные
	//   'one:1_10' - даст все значения от 1 до 10 включительно
	// данный оператор работает также
	//   с числами c десятичными долями (числа с плавающей точкой вида 0.1)
	//   с отрицательными числами
	// будьте внимательны:
	//   все сравниваемые строки будут преобразованы в число
	//   пробелы уберутся
	//   запятые автоматически преобразуются в точки
	//   точки будут считаться разделителями десятичных долей, а не разрядов
	// например:
	//   10.000 будет преобразовано в 10
	//   0,5 будет преобразовано в 0.5
	//   false будет преобразовано в 0
	//   true будет преобразовано в 1
	//   'string' будет преобразовано в 0
	// если вы хотите найти строку с нижним подчеркиванием или знаком больше, используйте оператор '*', т.к. он имеет приоритет:
	//   '*_' или '_*' или '*>' или '>*'
	//   '*_string' или '_string*' или '*>string' или '>string*'
	//   '*string_' или 'string_*' или '*string>' или 'string>*'
	
	// важно учесть, что если функция отдает на выходе false, это может говорить о том,
	// что она была вызвана с неправильными параметрами
	// соответственно, можно обработать вызов, например, так:
	// if (!dbUse(...)) { error }
	// или так:
	// $var = dbUse(...);
	// if ($var) { ... } else { error }
	
	if (
		!set($name) ||
		!set($query) ||
		!in_array($query, ['select', 'filter', 'write', 'delete', 'create', 'count', 'verify', 'connect']) ||
		(in_array($query, ['write']) && (!defined('isWRITE') || !isWRITE) && $name !== 'attempts') ||
		(in_array($query, ['delete', 'create']) && (!defined('isWRITE') || !isWRITE))
	) {
		return false;
	}
	
	$result = null;
	$rights = [];
	$original = $query === 'filter' ? $name : null;
	$name = $query === 'filter' ? null : dataParse($name);
	
	// здесь мы избавляем имя от точек
	
	if (objectIs($name)) {
		foreach ($name as &$item) {
			if (in_array($query, ['select', 'count', 'verify', 'delete'])) {
				$item = str_replace('.', '--', $item);
			} elseif (in_array($query, ['write', 'create'])) {
				$item = str_replace('.', '--', $item);
				//$item = str_replace('--', '.', $item);
			}
		}
		unset($item);
	}
	/*
	if (objectIs($parameters)) {
		foreach ($parameters as &$item) {
			if (objectIs($item)) {
				if (!empty($item['name'])) {
					if (in_array($query, ['select', 'count', 'verify', 'delete'])) {
						//$item['name'] = str_replace('--', '.', $item['name']);
						$item['name'] = str_replace('.', '--', $item['name']);
					} elseif (in_array($query, ['write', 'create'])) {
						$item['name'] = str_replace('.', '--', $item['name']);
					}
				} else {
					foreach ($item as &$i) {
						if (in_array($query, ['select', 'count', 'verify', 'delete'])) {
							//$i = str_replace('--', '.', $i);
							$i = str_replace('.', '--', $i);
						} elseif (in_array($query, ['write', 'create'])) {
							$i = str_replace('.', '--', $i);
						}
					}
					unset($i);
				}
			}
			echo '[' . $query . ':' . print_r($item, 1) . ']<br>';
			unset($item);
		}
	}
	*/
	
	// задаем объект, содержащий все необходимые данные, с которым и будем работать
	
	$file = [
		'db' => objectIs($name) ? reset($name) : null,
		'path' => objectIs($name) ? PATH_DATABASE . array_shift($name) : null,
		'line' => set($name, true),
		'format' => isset($parameters['format']) ? $parameters['format'] : true,
		'filelist' => [],
		'name' => [],
		'stat' => [],
		'info' => [],
		'data' => []
	];
	
	unset($name);
	
	// здесь, очевидно, по проверке констант нужно вызывать функцию,
	// которая будет читать права пользователя
	// и мержить массив параметров
	// ну или посылать запрос нафиг, если нет прав доступа к этой базе
	// ...
	
	if (SECURE_RIGHTS) {
		
		if (
			defined('isSYSTEM') && isSYSTEM && defined('isWRITE') && isWRITE ||
			$file['db'] === 'attempts'
		) {
			// здесь необходима проверка триггера isSYSTEM, потому что он разрешает полный доступ к базе,
			// а без него права на запись все равно будут устанавливаться согласно правам пользователя,
			// триггер isWRITE дает лишь ВОЗМОЖНОСТЬ записи, но не права на изменение всей базы данных
			$rights = [
				'read' => true,
				'write' => true,
				'create' => true,
				'delete' => true
			];
		} else {
			global $user;
			$rights = set($user -> rights, true);
		}
		
		$rights = objectRights($file['db'], $query, $rights);
		
		if ($rights === false) {
			// потом это надо будет вообще заменить на системный error
			logging('false rights on base [' . $file['db'] . '] with [' . $query . '] query');
			return false;
		}
		
	}
	
	// дополнительная часть кода, которая позволяет использовать имя строки
	// при вызове драйвера через двоеточие после имени раздела базы данных
	// в операциях 'write', 'delete' и 'create'
	
	if (
		in_array($query, ['write', 'delete', 'create']) &&
		!empty($file['line'])
	) {
		$newparam = [];
		$associate = !empty($parameters) && is_array($parameters) ? objectKeys($parameters) : true;
		foreach ($file['line'] as $item) {
			$newparam[] = array_merge(
				$associate ? $parameters : reset($parameters),
				['name' => $item]
			);
		}
		$parameters = $newparam;
		unset($newparam, $associate);
	}
	
	if ($query === 'select') {
		
		// настраиваем параметры по-умолчанию
		
		if ($parameters === true) {
			$parameters = [];
			$parameters['return'] = 'name:data';
			if ($file['db'] === 'structures') {
				$file['format'] = 'structure';
			} elseif ($file['db'] === 'content') {
				$file['format'] = 'content';
			}
		}
		
		//print_r($parameters);
		//echo '<br><br>';
		
		return dbLocal_select($file, $parameters, $rights);
		
	} elseif ($query === 'filter') {
		
		return dbLocal_filter($original, $parameters);
		
	} elseif ($query === 'write') {
		
		// запись в базу данных
		// создание новой записи или изменение существующей
		
		return dbLocal_write($file, $parameters, $rights);
		
	} elseif ($query === 'create') {
		
		// создание новой таблицы, раздела в базе данных
		
		return dbLocal_create($file, $parameters, $rights);
		
	} elseif ($query === 'delete') {
		
		// удаление записи из базы данных
		
		return dbLocal_delete($file, $parameters, $rights);
		
	}
	
}

/* ВСПОМОГАТЕЛЬНЫЕ ФУНКЦИИ ПО РАБОТЕ С БАЗОЙ ДАННЫХ */

function dbLocal_select($file, $parameters = [], $rights) {
	
	/*
	*  Эта функция эмулирует подключение к локальной базе данных
	*  
	*  На самом деле она смотрит указанное имя,
	*  определяет, что это - папка или файл, извлекает оттуда данные
	*  и возвращает в формате массива
	*/
	
	$datafile = [];
	$count = 0;
	$id = 0;
	
	$parameters = objectFill($parameters, ['allow', 'deny', 'filter', 'return']);
	
	if (!empty($parameters['return'])) {
		$parameters['return'] = dataParse($parameters['return']);
	}
	
	$return_key = set($parameters['return'][0], true);
	$return_data = set($parameters['return'][1], true);
	$return_alone = false;
	
	if ($return_key === 'alone') {
		$return_alone = true;
		$return_key = 'name';
	}
	
	// на самом деле хотелось бы упростить выборку файла, но проблема в том, что
	// имя может быть составным из id, type и прочих параметров
	
	if (
		file_exists($file['path'] . '.ini') &&
		is_file($file['path'] . '.ini')
	) {
		
		// указанная таблица - это файл базы данных
		
		$file = array_merge(
			$file,
			dbLocal_readfile($file['path'] . '.ini', $file['format'])
			// вот здесь мы вызываем файл на чтение
		);
		
		// здесь мы пробует отсечь лишние записи в таблице
		if (set($file['line'])) {
			$file['filelist'] = array_keys($file['data']);
			$file['filelist'] = dbLocal_reduce($file['filelist'], $file['line']);
			$file['data'] = array_intersect_key($file['data'], array_flip($file['filelist']));
		}
		
		// здесь мы сортируем массив, если задан соответствующий параметр
		// сортируем после чтения, т.к. все строки - внутри файла
		if (!empty($parameters['sort'])) {
			$file['data'] = dbLocal_sortdata($file['data'], $parameters['sort']);
			$file['data'] = $file['data'][0];
		}
		
		foreach ($file['data'] as $key => $item) {
			
			// разбираем имя
			$file['name'] = dbLocal_parsename($key, $id++);
			//echo '[file] : ' . $file['path'] . '.ini (' . $key . ')<br>' . print_r($file['name'], true) . '<hr>';
			
			// фильтруем строку по name
			if (set($file['line']) && !in_array($file['name']['name'], $file['line'])) {
				continue;
			}
			
			// устанавливаем родителя
			// новый код, родитель - массив папок, начиная после папки раздела базы данных
			if (empty($file['name']['parent'])) {
				$file['info']['dirname'] = substr($file['info']['dirname'], strlen($file['path']) - 1);
				// вот здесь нужно проверить, правильно ли работает -1 в пути
				// работает правильно, потому что несмотря ни на что, либо слеша нет, либо он появляется, но потом режется по сплиту в следующем условии
				if (!empty($file['info']['dirname'])) {
					$file['name']['parent'] = datasplit($file['info']['dirname'], '\\' . DS);
				}
			}
			
			// устанавливаем временные метки
			$file['name']['mtime'] = $file['stat'][9];
			$file['name']['ctime'] = $file['stat'][10];
			
			// фильтруем строку по allow/deny/self/exclude
			if (SECURE_RIGHTS) {
				
				$self = false;
				if (set($rights['self']) || set($rights['exclude'])) {
					global $user;
					if (
						!empty($user -> name) &&
						objectIs($file['name']['self']) &&
						in_array($user -> name, $file['name']['self'])
					) {
						$self = true;
					}
				}
				
				if ($self) {
					
					if (
						set($rights['exclude']) &&
						is_array($rights['exclude']) &&
						!dbLocal_filterdata($rights['exclude'], $file['name'], true)
					) {
						continue;
					} elseif (
						objectIs($rights['fields']['exclude'])
					) {
						$item = array_diff_key($item, array_flip($rights['fields']['exclude']));
					}
					if (
						set($rights['self']) &&
						is_array($rights['self']) &&
						!dbLocal_filterdata($rights['self'], $file['name'])
					) {
						continue;
					} elseif (
						objectIs($rights['fields']['self'])
					) {
						$item = array_intersect_key($item, array_flip($rights['fields']['self']));
					}
					
				} else {
					
					if (
						set($rights['deny']) &&
						is_array($rights['deny']) &&
						!dbLocal_filterdata($rights['deny'], $file['name'], true)
					) {
						continue;
					} elseif (
						objectIs($rights['fields']['deny'])
					) {
						$item = array_diff_key($item, array_flip($rights['fields']['deny']));
					}
					if (
						set($rights['allow']) &&
						is_array($rights['allow']) &&
						!dbLocal_filterdata($rights['allow'], $file['name'])
					) {
						continue;
					} elseif (
						objectIs($rights['fields']['allow'])
					) {
						$item = array_intersect_key($item, array_flip($rights['fields']['allow']));
					}
					
				}
				
			}
			
			// фильтруем строку по allow/deny
			if (set($parameters['allow'])) {
				if (dbLocal_filterdata($parameters['allow'], $file['name'])) {
					continue;
				}
			}
			if (set($parameters['deny'])) {
				if (!dbLocal_filterdata($parameters['deny'], $file['name'], true)) {
					continue;
				}
			}
			
			// фильтруем строку по filter
			if (set($parameters['filter'])) {
				if (dbLocal_filterdata($parameters['filter'], $item, $parameters['or'])) {
					continue;
				}
			}
			
			// фильтруем строку по skip, если был задан пропуск материалов
			if (!empty($parameters['skip'])) {
				$parameters['skip']--;
				continue;
			}
			
			$count++;
			
			// задаем ключ нового массива по return
			if ($return_key === 'name') {
				$k = $file['name']['name'];
			} elseif ($return_key === 'id') {
				$k = $file['name']['id'];
			} else {
				$k = $count - 1;
			}
			
			// задаем содержимое нового массива по return
			if ($return_data) {
				$table[$k] = $item;
			} else {
				$table[$k] = array_merge(
					$file['name'],
					[ 'data' => $item ]
				);
			}
			
			// фильтруем строку по limit или return
			if (
				!empty($parameters['limit']) && $count >= $parameters['limit'] ||
				$return_alone
			) {
				break;
			}
			
		}
		unset($key, $item);
		
	} elseif (
		file_exists($file['path']) &&
		is_dir($file['path'])
	) {
		
		// указанная таблица - это папка с файлами
		
		$file['path'] .= DS;
		$file['filelist'] = localList($file['path'], ['return' => 'files', 'subfolders' => true, 'type' => 'ini']);
		
		// здесь мы пробуем отсечь лишние записи в таблице,
		if (set($file['line'])) {
			$file['filelist'] = dbLocal_reduce($file['filelist'], $file['line']);
		}
		
		// здесь мы сортируем массив, если задан соответствующий параметр
		// сортируем до чтения, т.к. все строки - это файлы, которые будут читаться,
		// но при этом возможен вариант, что нам придется прочесть файл,
		// если сортировка задана не по id или name, а по полю, расположенному в данных внутри файла
		// поэтому мы передаем путь к файлу и его формат
		
		if (!empty($parameters['sort'])) {
			$file['filelist'] = dbLocal_sortdata(
				$file['filelist'],
				$parameters['sort'],
				[
					'path' => $file['path'],
					'format' => $file['format']
				]
			);
			// сложность лишь в том, что тогда получается, мы будем читать файл два раза
			// поэтому мы делаем дополнительный массив, куда читаем данные файла и проверяем его в цикле ниже
			$datafile = $file['filelist'][1];
			$file['filelist'] = array_flip($file['filelist'][0]);
		}
		
		foreach ($file['filelist'] as $key => $item) {
			
			if (empty($datafile[$item])) {
				$datafile[$item] = dbLocal_readfile($file['path'] . $item, $file['format']);
				//echo '[read in forech]';
				// вот здесь мы вызываем файл на чтение,
				// но только при условии, что он до этого не был прочитан при сортировке
			}
			
			$file = array_merge(
				$file,
				$datafile[$item]
			);
			unset($datafile[$item]);
			
			// разбираем имя
			$file['name'] = dbLocal_parsename(str_replace('.', ':', $file['info']['filename']), $id++);
			//echo '[dir] : ' . $file['path'] . ' (' . $item . ')<br>' . print_r($file['name'], true) . '<hr>';
			
			// фильтруем строку по name
			if (set($file['line']) && !in_array($file['name']['name'], $file['line'])) {
				continue;
			}
			
			// устанавливаем родителя
			// новый код, родитель - массив папок, начиная после папки раздела базы данных
			if (empty($file['name']['parent'])) {
				$file['info']['dirname'] = substr($file['info']['dirname'], strlen($file['path']) - 1);
				// вот здесь нужно проверить, правильно ли работает -1 в пути
				// работает правильно, потому что несмотря ни на что, либо слеша нет, либо он появляется, но потом режется по сплиту в следующем условии
				if (!empty($file['info']['dirname'])) {
					$file['name']['parent'] = datasplit($file['info']['dirname'], '\\' . DS);
				}
			}
			
			// устанавливаем временные метки
			$file['name']['mtime'] = $file['stat'][9];
			$file['name']['ctime'] = $file['stat'][10];
			
			// фильтруем строку по allow/deny/self/exclude
			if (SECURE_RIGHTS) {
				
				$self = false;
				if (set($rights['self']) || set($rights['exclude'])) {
					global $user;
					if (
						!empty($user -> name) &&
						objectIs($file['name']['self']) &&
						in_array($user -> name, $file['name']['self'])
					) {
						$self = true;
					}
				}
				
				if ($self) {
					
					if (
						set($rights['exclude']) &&
						is_array($rights['exclude']) &&
						!dbLocal_filterdata($rights['exclude'], $file['name'], true)
					) {
						continue;
					} elseif (
						objectIs($rights['fields']['exclude'])
					) {
						$file['data'] = array_diff_key($file['data'], array_flip($rights['fields']['exclude']));
					}
					if (
						set($rights['self']) &&
						is_array($rights['self']) &&
						!dbLocal_filterdata($rights['self'], $file['name'])
					) {
						continue;
					} elseif (
						objectIs($rights['fields']['self'])
					) {
						$file['data'] = array_intersect_key($file['data'], array_flip($rights['fields']['self']));
					}
					
				} else {
					
					if (
						set($rights['deny']) &&
						is_array($rights['deny']) &&
						!dbLocal_filterdata($rights['deny'], $file['name'], true)
					) {
						continue;
					} elseif (
						objectIs($rights['fields']['deny'])
					) {
						$file['data'] = array_diff_key($file['data'], array_flip($rights['fields']['deny']));
					}
					if (
						set($rights['allow']) &&
						is_array($rights['allow']) &&
						!dbLocal_filterdata($rights['allow'], $file['name'])
					) {
						continue;
					} elseif (
						objectIs($rights['fields']['allow'])
					) {
						$file['data'] = array_intersect_key($file['data'], array_flip($rights['fields']['allow']));
					}
					
				}
				
			}
			
			// фильтруем строку по filter
			if (set($parameters['filter'])) {
				if (dbLocal_filterdata($parameters['filter'], $file['data'], $parameters['or'])) {
					continue;
				}
			}
			
			// фильтруем строку по allow/deny
			if (set($parameters['allow'])) {
				if (dbLocal_filterdata($parameters['allow'], $file['name'])) {
					continue;
				}
			}
			if (set($parameters['deny'])) {
				if (!dbLocal_filterdata($parameters['deny'], $file['name'], true)) {
					continue;
				}
			}
			
			// фильтруем строку по skip, если был задан пропуск материалов
			if (!empty($parameters['skip'])) {
				$parameters['skip']--;
				continue;
			}
			
			$count++;
			
			// задаем ключ нового массива по return
			if ($return_key === 'name') {
				$k = $file['name']['name'];
			} elseif ($return_key === 'id') {
				$k = $file['name']['id'];
			} else {
				$k = $count - 1;
			}
			
			// задаем содержимое нового массива по return
			if ($return_data) {
				$table[$k] = $file['data'];
			} else {
				$table[$k] = array_merge(
					$file['name'],
					[ 'data' => $file['data'] ]
				);
			}
			
			// фильтруем строку по limit
			if (
				!empty($parameters['limit']) && $count >= $parameters['limit'] ||
				$return_alone
			) {
				break;
			}
			
		}
		unset($key, $item);
		
	}
	
	unset($file, $datafile, $parameters);
	
	if ($return_alone && !empty($table)) {
		$table = array_shift($table);
	}
	
	return $table;
	
}

function dbLocal_readfile($path, $format = true) {
	
	/*
	*  Эта функция эмулирует чтение записи из базы данных
	*  
	*  На самом деле она получает данные
	*  из указанного файла, а также об этом файле
	*  и возвращает в формате массива,
	*  как будто это часть строки из базы данных
	*/
	
	if (!file_exists($path)) {
		return [];
	}
	
	return [
		'stat' => stat($path),
		'info' => pathinfo($path),
		'data' => iniPrepareJson(localOpenFile($path), $format)
		// вот в этой строке происходит непосредственно подключение к файлу,
		// чтение его содержимого и обработка
	];
	
}

function dbLocal_reduce($list, $line = false) {
	
	/*
	*  Эта функция пробует отсечь лишние записи в таблице
	*  
	*  На самом деле она получает данные в виде массива,
	*  поочередно ищет в них совпадение
	*  и оставляет только те, которые совпали
	*  
	*  Это, конечно, немного медленнее, чем простой фильтр
	*  по имени, особенно в больших массивах,
	*  зато намного быстрее, чем открывать файлы,
	*  а затем сравнивать имена внутри них.
	*  Учитывая, что имена, как в принципе, желательно делать составными,
	*  простое исключение по имени применять не будем.
	*/
	
	if (!set($line)) {
		return $list;
	}
	
	if (count($line) === 1) {
		
		// если строка выбора всего одна, зачем еще заморачиваться на много переборов,
		// когда можно обойтись всего одним
		// при этом здесь исключаются все записи массива, не попадающие под шаблон поиска
		
		$line = array_shift($line);
		foreach ($list as $key => $item) {
			if (strpos($item, $line) === false) {
				unset($list[$key]);
			}
		}
		unset($key, $item, $line);
		
		return $list;
		
	} else {
		
		// если строк выборки несколько, простым перебором здесь не обойтись
		// исключать записи по сравнению тоже нельзя, потому что тогда будут удалены ВСЕ записи
		// ведь во втором переборе каждая запись будет не совпадать
		// поэтому проще не удалять, а формировать новый массив значений
		
		$arr = [];
		
		foreach ($list as $key => $item) {
			foreach ($line as $i) {
				if (strpos($item, $i) !== false) {
					$arr[$key] = $item;
				}
			}
		}
		
		unset($key, $item, $i, $line, $list);
		
		return $arr;
		
	}
	
}

function dbLocal_parsename($name, $count) {
	
	/*
	*  Эта функция эмулирует преобразование записи из базы данных
	*  
	*  На самом деле она смотрит указанное имя,
	*  распарсивает его на id, name и type,
	*  и возвращает в формате массива,
	*  как будто это часть строки из базы данных
	*/
	
	$entry = [
		'id' => '',
		'name' => '',
		'type' => '',
		'self' => [],
		'parent' => null,
		'ctime' => null,
		'mtime' => null
	];
	
	// здесь parent, ctime и mtime не разбираем, т.к. на вход подается только имя файла, а не путь
	// но мы здесь задаем просто пустые элементы массива, которые будут возвращаться
	// и которые мы будем использовать после возврата для разбора уже там
	
	if (strpos($name, DS) !== false) {
		$name = substr($name, strrpos($name, DS) + 1);
	}
	//$name = dataParse($name);
	$name = datasplit($name, ':', false);
	
	if (is_numeric($name[0]) && !empty($name[1])) {
		$entry['id'] = (int) array_shift($name);
	} else {
		$entry['id'] = $count;
	}
	
	// старый код
	//$entry['name'] = $name[0];
	
	// новый код для разбора родителя
	if (strpos($name[0], '.') !== false) {
		$name[0] = datasplit($name[0], '.');
		$entry['name'] = array_shift($name[0]);
		$entry['parent'] = $name[0];
	} else {
		$entry['name'] = $name[0];
	}
	
	$entry['type'] = !empty($name[1]) ? $name[1] : null;
	$entry['self'] = !empty($name[2]) ? datasplit($name[2]) : null;
	
	unset($name, $count);
	
	return $entry;
	
}

function dbLocal_sortdata($data, $sort = false, $file = []) {
	
	/*
	*  Эта функция сортирует файлы
	*  
	*  На самом деле она подготавливает массив с ключами, сортирует его,
	*  а затем заполняет значениями из исходного массива
	*  
	*  Первым параметром указывается исходный массив 
	*  Вторым параметром указываются данные сортировки
	*  
	*  На выходе отдает отсортированный или исходный массив
	*/
	
	if (
		empty($sort) || !is_string($sort) || !objectIs($data)
	) {
		return [$data, []];
	}
	
	$sort = dataParse($sort);
	$sortkeys = [];
	$datafile = [];
	
	if (!set($sort[0])) {
		return [$data, []];
	}
	
	if (!empty($sort[2])) {
		
		if ($sort[2] === 'string') {
			$sort[2] = SORT_LOCALE_STRING;
		} elseif ($sort[2] === 'numeric') {
			$sort[2] = SORT_NUMERIC;
		} else {
			$sort[2] = SORT_REGULAR;
		}
		
	}
	
	//echo '<pre>' . print_r($sortkeys, true) . '</pre>';
	
	$count = 0;
	
	foreach ($data as $key => $item) {
		
		if (!empty($file)) {
			$key = str_replace(['.ini', '.'], ['',':'], $item); // dir
		}
		
		//echo '[' . print_r($key, 1) . ']<br>';
		
		if (
			$sort[0] === 'id' ||
			$sort[0] === 'name'
		) {
			$newkey = dbLocal_parsename($key, $count++);
			$newkey = $newkey[$sort[0]];
		} elseif (!empty($file)) {
			// dir
			$f = dbLocal_readfile($file['path'] . $item, $file['format']);
			//echo '[read in sort]';
			if (isset($f['data'][$sort[0]])) {
				$newkey = $f['data'][$sort[0]];
			} else {
				$newkey = 0;
			}
			// сюда можно добавить код,
			// согласно которому данные файла будут записываться
			// в отдельную переменную и передаваться затем вместе с обратными данными
			$datafile[$item] = $f;
			unset($f);
		} elseif (isset($item[$sort[0]])) {
			$newkey = $item[$sort[0]];
		}
		
		if (!empty($file)) {
			$key = $item; // dir
		}
		
		$sortkeys[$key] = $newkey;
	}
	
	unset($count, $newkey, $key, $item);
	
	if ($sort[1] === 'shuffle') {
		$sortkeys = array_keys($sortkeys);
		shuffle($sortkeys);
	} else {
		if ($sort[1] === 'desc') {
			arsort(
				$sortkeys,
				empty($sort[2]) ? SORT_NATURAL | SORT_FLAG_CASE : $sort[2]
			);
			
		} else {
			asort(
				$sortkeys,
				empty($sort[2]) ? SORT_NATURAL | SORT_FLAG_CASE : $sort[2]
			);
		}
		$sortkeys = array_keys($sortkeys);
	}
	
	if (!empty($file)) {
		$data = array_flip($data);
	}
	
	$data = array_replace(array_flip($sortkeys), $data);
	
	unset($sort, $sortkeys);
	
	return [$data, $datafile];
	
}
	
function dbLocal_filterdata($filter, $item, $or = false) {
	
	/*
	*  Эта функция эмулирует разбор данных по строке filter
	*  и отдает команду - пропустить эту строку или оставить
	*  
	*  На самом деле она подготавливает и сравнивает два массива по ключам и значениям
	*  
	*  Первым параметром указывается строка filter
	*  Вторым параметром указывается массив данных
	*  Третий параметр необязательный, устанавливает тип сравнения по 'и' (по-умолчанию) или по 'или'
	*  
	*  На выходе отдает
	*  true - если строка попадает под фильтр filter и тогда ее нужно пропустить, исключить из результата
	*  false - если строка не попадает под фильтр и ее нужно оставить для дальнейшего разбора
	*/
	
	//echo print_r($filter, true) . '<br>';
	if (!is_array($filter)) {
		$filter = dataParse($filter, false);
	}
	
	//echo 'filter: ' . print_r($filter, true) . '<br>';
	//echo 'item: ' . print_r($item, true) . '<br>';
	
	$intersect = array_intersect_key($item, $filter);
	$continue = false;
	$in = false;
	
	//echo '<hr><hr>' . print_r($intersect, true) . '<hr><hr>';
	
	//if (set($intersect)) {
	if (!empty($intersect)) {
		foreach ($filter as $k => $i) {
			
			$k = mb_strtolower($k);
			//echo '[' . $k . ']<br>';
			//echo '<br>~~~' . $k . ' :: ' . print_r($i, true);
			//echo '<br> :: ' . print_r($i, true);
			
			if ($item[$k] === true) {
				$item[$k] = 'true';
			} elseif ($item[$k] === false) {
				$item[$k] = 'false';
			} elseif (is_array($item[$k])) {
				foreach ($item[$k] as $kk => &$ki) {
					$ki = mb_strtolower($ki);
				}
				unset($kk, $ki);
			} else {
				$item[$k] = mb_strtolower($item[$k]);
			}
			
			$search = [];
			
			if (set($i) && is_array($i)) {
				foreach ($i as $sk => &$sio) {
					$sio = mb_strtolower($sio);
					$si = $sio;
					if (mb_strpos($si, '+') !== false) {
						$search['type'] = empty($i[1]) ? 'all' : 'another';
						$search['value'] = empty($search['value']) || $search['type'] === 'all' ? datasplit($si, '+') : array_merge($search['value'], datasplit($si, '+'));
						if (!is_array($item[$k])) {
							$item[$k] = [$item[$k]];
						}
					} elseif (mb_strpos($si, '-') === 0) {
						$si = mb_substr($si, 1);
						$search['type'] = 'exclude';
						$search['value'][] = $si;
						//echo '<br>' . $k . ' :: ' . print_r($i, true) . ' :: ' . $si . ' :: ' . print_r($search['value'], true);
					} elseif (mb_strpos($si, '*') !== false) {
						$search['type'] = 'string';
						$search['value'] = str_replace('*', '', $si);
					} elseif (mb_strpos($si, '>') !== false) {
						$search['type'] = 'inarray';
						$search['value'] = str_replace('>', '', $si);
					} elseif (mb_strpos($si, '_') !== false && is_numeric(str_replace([',', '.', '_', ' '], '', $si))) {
						$search['type'] = 'math';
						$search['value'] = datasplit($si, '_');
						$item[$k] = (float) str_replace([',', ' '], ['.', ''], $item[$k]);
						if (mb_strpos($si, '_') === 0) {
							$search['value'] = [
								'min' => false,
								'max' => (float) $search['value'][0]
							];
						} else {
							$search['value'] = [
								'min' => (float) $search['value'][0],
								'max' => isset($search['value'][1]) ? (float) $search['value'][1] : false
							];
						}
					}
					unset($si);
				}
				unset($sk, $sio);
			}
			
			//echo '<br>' . $k . ' :: ' . print_r($search, true) . ' :: ' . print_r($item[$k], true);
			//echo '<br>' . $k . ' :: ' . print_r($i, true) . ' :: ' . print_r($search['value'], true) . ' :: ' . print_r($item[$k], true);
			//echo '<br>' . $k . ' :: ' . print_r($item[$k], 1) . ' :: ' . print_r(array_diff($item[$k], $search['value']), true) . ' :: ' . print_r(array_diff($search['value'], $item[$k]), true) . ' :: ' . print_r(array_intersect($item[$k], $search['value']), true) . ' :: ' . print_r(array_intersect($search['value'], $item[$k]), true);
			
			if (
				(set($i) && is_array($i) && (
					!set($search) && !is_array($item[$k]) && in_array($item[$k], $i) ||
					!set($search) && is_array($item[$k]) && !empty(array_intersect($item[$k], $i)) ||
					set($search['type']) && !set($search['value']) ||
					set($search['value']) && (
						(
							set($search['type'], true) === 'all' &&
							is_array($item[$k]) &&
							is_array($search['value']) &&
							empty(array_diff($item[$k], $search['value'])) &&
							empty(array_diff($search['value'], $item[$k]))
						) || (
							set($search['type'], true) === 'another' &&
							is_array($item[$k]) &&
							is_array($search['value']) &&
							empty(array_diff($search['value'], $item[$k]))
							//empty(array_diff($item[$k], $search['value']))
						) || (
							set($search['type'], true) === 'exclude' &&
							is_array($item[$k]) &&
							is_array($search['value']) &&
							empty(array_intersect($item[$k], $search['value']))
						) || (
							set($search['type'], true) === 'string' &&
							mb_stripos($item[$k], $search['value']) !== false
						) || (
							set($search['type'], true) === 'inarray' &&
							is_array($item[$k]) &&
							in_array($search['value'], $item[$k])
						) || (
							set($search['type'], true) === 'math' && (
								set($search['value']['min']) &&
								set($search['value']['max']) &&
								$item[$k] >= $search['value']['min'] &&
								$item[$k] <= $search['value']['max']
								||
								!set($search['value']['min']) &&
								$item[$k] <= $search['value']['max']
								||
								!set($search['value']['max']) &&
								$item[$k] >= $search['value']['min']
							)
						)
					)
				)) ||
				(!set($i) && set($item[$k]) && $item[$k] !== 'false')
			) {
				if (!empty($or)) { $in[] = true; } else { $in = true; }
			} else {
				if (!empty($or)) { $in[] = false; } else { $in = false; break; }
			}
			
		}
		unset($k, $i);
		if (!set($in)) { $continue = true; }
	} else {
		$continue = true;
	}
	
	unset($filter, $intersect, $in);
	return $continue;
	
}

function dbLocal_filter($data, $parameters = []) {
	
	/*
	*  Эта функция эмулирует... нет, ничего она не эмулирует
	*  Она сортирует имеющиеся данные, создавая новый массив.
	*  
	*  Это сделано для того, чтобы когда нужно отфильтровать уже полученные данные,
	*  не приходилось заново подключаться к базе данных или читать файлы
	*  
	*  первый параметр - это массив данных,
	*  второй параметр - набор параметров
	*  
	*  если указать параметр return = name/id, то будет возвращен массив с name или id элементов
	*  если указать data, то будут возвращено только содержимое данных массива
	*  кстати, data и пустое значение не изменяют ключи исходного массива
	*/
	
	$count = 0;
	
	// устанавливаем переменные вывода
	if (!empty($parameters['return'])) {
		$parameters['return'] = dataParse($parameters['return']);
	}
	
	$return_key = set($parameters['return'][0], true);
	$return_alone = false;
	
	if ($return_key === 'alone') {
		$return_alone = true;
		$return_key = 'name';
	}
	
	// подготавливаем два массива
	// один - для сортировки и фильтрации,
	// второй - для сохранения связи первого с ключами оригинала
	foreach ($data as $key => $item) {
		
		// фильтруем строку по allow/deny
		if (set($parameters['allow'])) {
			if (dbLocal_filterdata($parameters['allow'], $item)) {
				continue;
			}
		}
		if (set($parameters['deny'])) {
			if (!dbLocal_filterdata($parameters['deny'], $item, true)) {
				continue;
			}
		}
		
		//$newarr[$item['name']] = $item['data'];
		$newarr[$item['id'] . ':' . $item['name']] = $item['data'];
		$keysarr[$item['id'] . ':' . $item['name']] = $key;
		
	}
	unset($key, $item);
	
	//print_r($data);
	
	if (!empty($newarr)) {
		
		// здесь мы сортируем массив, если задан соответствующий параметр
		if (!empty($parameters['sort'])) {
			$newarr = dbLocal_sortdata($newarr, $parameters['sort']);
			$newarr = $newarr[0];
		}
		
		foreach ($newarr as $key => $item) {
			
			// фильтруем строку по filter
			
			if (set($parameters['filter'])) {
				if (dbLocal_filterdata($parameters['filter'], $item, $parameters['or'])) {
					continue;
				}
			}
			
			// фильтруем строку по skip, если был задан пропуск материалов
			if (!empty($parameters['skip'])) {
				$parameters['skip']--;
				continue;
			}
			
			$count++;
			
			// новые условия
			if ($return_key === 'name') {
				$table[] = $key;
			} elseif ($return_key === 'id') {
				$table[] = $keysarr[$key];
			} elseif ($return_key === 'data') {
				$table[$keysarr[$key]] = $item;
			} else {
				$table[$keysarr[$key]] = $data[$keysarr[$key]];
			}
			
			/*
			// старые условия
			if ($return_key === 'name') {
				$table[] = $key;
			} elseif ($return_key === 'id') {
				$table[] = $keysarr[$key];
			} else {
				$table[] = $data[$keysarr[$key]];
			}
			*/
			
			// фильтруем строку по limit
			if (
				!empty($parameters['limit']) && $count >= $parameters['limit'] ||
				$return_alone
			) {
				break;
			}
			
		}
		
		unset($k, $key, $item, $count, $parameters, $keysarr, $newarr, $data);
		//echo '<pre>' . print_r($table, true) . '</pre>';
		
	}
	
	if ($return_alone && !empty($table)) {
		$table = array_shift($table);
	}
	
	return $table;
	
}

function dbLocal_write($file, $parameters, $rights) {
	
	/*
	*  Функция эмулирует запись в базу данных
	*  
	*  объекты:
	*  $file = [
	*    'db' => reset($name),
	*    'path' => PATH_DATABASE . array_shift($name),
	*    'line' => set($name, true),
	*    ... неважно
	*  ]
	*  
	*  id - необязательно для файла, но обязательно для таблицы
	*  name - соответствует имени файла с расширением ini
	*  type - тип данных, для поиска и фильтрации определенных данных внутри одного раздела
	*  parent - родитель, к какому семейству относится эта запись, в случае локальной базы данных, это - название родительской папки
	*  ctime - время создания записи
	*  mtime - время последнего изменения записи
	*  self - это поле содержит идентификаторы пользователей, которые являются авторами или последними, кто вносил изменения
	*  data - данные в формате json
	*  
	*  $parameters = []
	*  это данные, которые нужно записать
	*  $rights = []
	*  это права
	*/
	
	/*
	*  Функция эмулирует запись в базу данных через апдейт
	*  на данный момент это выглядит просто как удаление предыдущей записи и запись новой целиком
	*  
	*  фактически надстройка над dbLocal_insert
	*  
	*  ПО ИДЕЕ, ВОЗМОЖНО ПОЗЖЕ, ЭТА ФУНКЦИЯ ДОЛЖНА ПРЕДВАРИТЕЛЬНО ЧИТАТЬ ДАННЫЕ И МЕРДЖИТЬ ИХ
	*  И ТОЛЬКО ПОТОМ УДАЛЯТЬ
	*  
	*/
	
	if (!objectIs($parameters)) {
		return false;
	}
	
	if (file_exists($file['path'] . '.ini')) {
		
		$target = 'file';
		//unlink($file['path'] . '.ini');
		//теперь не удаляем - раньше удаляли только для update, но не для insert
		
	} else {
		
		$target = 'dir';
		$file['path'] .= DS;
		
	}
	
	// вот это вот деление на target выше нужно для того, чтобы разбираться, как именно добавлять инфу
	// то ли создавать новый файл, то ли читать старый, разбирать как массив, объединять и записывать обратно
	// пока же нам это все до лампочки и мы просто тупо создаем новые файлы для каждой записи
	
	// кстати, в любом случае нужно проверить код на строках 292, 491 по описанию "устанавливаем родителя" (если вдруг строки сместятся)
	
	$name = null;
	$result = [];
	
	$data = [
		'id' => null,
		'name' => null,
		'type' => null,
		'parent' => [],
		'self' => [],
		'data' => null
	];
	
	foreach ($parameters as $item) {
		
		//echo '[<pre>' . print_r($item, 1) . ']</pre><br>';
		
		if (!objectIs($item)) {
			logging('wrong parameters in write to database \'' . $file['db'] . '\'', 'wrong in write to database');
			break;
		}
		
		$merge = null;
		
		// здесь мы ищем существующий файл по частям имени (т.к. файл может не содержать, например, id или parent, type или self и др. параметров)
		// в данной версии мы просто удаляем существующий файл, чтобы записать поверх него новый
		// однако потом, в зависимости от параметра, мы можем его сперва читать и мерджить данные, и только затем удалять
		
		$files = glob($file['path'] . (!empty($item['parent']) ? str_replace('..', '', objectToString($item['parent'], DS)) . DS : null) . (!empty($item['id']) ? $item['id'] . '.' : null) . $item['name'] . '*.ini');
		
		//echo '<br>FILES:<br>' . print_r($files, true) . '<br>';
		
		if (!empty($files)) {
			foreach ($files as $i) {
				if (preg_match('/([\d]\.)?' . $item['name'] . '(\.\w*)?\.ini/', str_ireplace($file['path'], '', $i))) {
					if (empty($item['clear'])) {
						$merge = dbLocal_readfile($i, $file['format']);
						//echo '{{' . $file['path'] . '}}';
						$merge = $merge['data'];
					} else {
						//unlink($i);
					}
					//echo '<br>' . $i . '<br>';
				}
			}
		}
		
		unset($files, $p, $i);
		
		// здесь мы объединяем данные
		
		if (objectIs($item['data'])) {
			if (objectIs($merge)) {
				$item['data'] = array_merge($merge, $item['data']);
			}
			$item['data'] = iniPrepareArray($item['data'], true);
		}
		$item = array_merge($data, $item);
		unset($merge);
		
		// а вот здесь у нас копия кода из записи файла
		
		$name = 
			set($item['parent'], objectToString($item['parent'], DS) . DS) . 
			(is_numeric($item['name']) ? set($item['id'], $item['id'] . '.') : null) . 
			$item['name'] . 
			set($item['type'], '.' . $item['type']) . 
			set($item['self'], '.' . objectToString($item['self'])) . 
		'.ini';
		
		$i = $file['path'];
		foreach ($item['parent'] as $ii) {
			$i .= $ii . DS;
			if (!file_exists($i)) {
				mkdir($i);
			}
		}
		unset($i, $ii);
		//echo '[<br>name:' . print_r($name, true) . '<br>file:' . print_r($file, true) . '<br>:parameters' . print_r($parameters, true) . '<br>:rights' . print_r($rights, true) . '<br>]';
		
		$result[] = file_put_contents($file['path'] . $name, $item['data']);
		
		// старое условие, которое создавало файл, только если он отсутствует:
		//if (!file_exists($file['path'] . $name)) {
		//	$result[] = file_put_contents($file['path'] . $name, $item['data']);
		//}
		
		//echo print_r($item, true) . '<br><br>' . $file['path'] . ' :: ' . $name . '<hr>';
		
	}
	
	unset($item);
	
	// если все ок, все записал, то true
	// а если ничего не записал (может быть, файл уже существовал или где-то ошибка), то false
	
	return set($result);
	
}

function dbLocal_delete($file, $parameters, $rights) {
	
	/*
	*  Функция эмулирует удаление записей из базы данных
	*/
	
	$file['path'] .= DS;
	$result = [];
	
	foreach ($parameters as $item) {
		
		$files = glob($file['path'] . (!empty($item['parent']) ? objectToString($item['parent'], DS) . DS : null) . (!empty($item['id']) ? $item['id'] . '.' : null) . $item['name'] . (!empty($item['type']) ? $item['type'] . '.' : null) . '*.ini');
		
		$folders = localList($file['path'], ['return' => 'folders']);
		if (!empty($folders) && is_array($folders)) {
			foreach ($folders as $i) {
				$files = array_merge($files, glob($file['path'] . $i . (!empty($item['parent']) ? objectToString($item['parent'], DS) . DS : null) . (!empty($item['id']) ? $item['id'] . '.' : null) . $item['name'] . (!empty($item['type']) ? $item['type'] . '.' : null) . '*.ini'));
			}
			unset($i);
		}
		unset($folders);
		
		//echo '<br>FILES:<br>' . print_r($files, true) . '<br>';
		
		if (!empty($files)) {
			foreach ($files as $i) {
				if (preg_match('/([\d]\.)?' . $item['name'] . '(\.\w*)?\.ini/', str_ireplace($file['path'], '', $i))) {
					unlink($i);
					$result[] = $item['name'];
					//echo '<br>' . $i . '<br>';
				}
			}
		}
		
		unset($files, $i);
		
	}
	
	unset($item);
	
	return set($result);
	
}

function dbLocal_create($file, $parameters, $rights) {
	
	if (!file_exists($file['path'])) {
		mkdir($file['path']);
	}
	
	if (set($parameters) && objectIs($parameters)) {
		dbLocal_write($file, $parameters, $rights);
	}
	
}

?>