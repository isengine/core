<?php

function dbPDOConnect(){
	
	/*
	*  Функция подключения к базе данных через библиотеку PDO
	*  на входе ничего указывать не нужно
	*
	*  функция берет все данные из констант и устанавливает соединение с базой данных
	*  в случае ошибки возвращает сообщение
	*/
	
	$db = new PDO(DB_TYPE . ':host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8', DB_USER, DB_PASS, array(
		//PDO::ATTR_PERSISTENT         => true
		//PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        //PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        //PDO::ATTR_EMULATE_PREPARES   => false,
	));
	
	return $db;
	
}

/* ФУНКЦИИ ДЛЯ РАБОТЫ С БАЗОЙ ДАННЫХ ПОЛЬЗОВАТЕЛЯ */

// ПОПЫТКИ АВТОРИЗАЦИИ

// универсальная функция записи попыток авторизации в базу данных
// универсальная функция удаления записи о попытке авторизации в базу данных
// универсальная функция чтения параметров попыток авторизации из базы данных

function dbAttempts($status, $array) {
	
	/*
	*  Универсальная функция управления попытками авторизации
	*  данные на входе - статус и массив значений
	*    status = save - сохранить новое значение
	*    status = update - перезаписать старое значение
	*    status = delete - удалить значение
	*    status = verify - проверить значение
	*  
	*  на выходе отдается массив со значениями из базы данных,
	*  но только для статуса verify
	*/
	
	if (DB_TYPE && DB_TYPE !== 'nodb') {
		
		$connect = dbPDOConnect();
		
		if ($status == 'save') {
			$query = 'INSERT INTO ' . DB_PREFIX . '_attempts(ip, session, ban, counts, data) VALUES(:ip, :session, :ban, :counts, :data)';
		} elseif ($status == 'update') {
			$query = 'UPDATE ' . DB_PREFIX . '_attempts SET session=:session, ban=:ban, counts=:counts, data=:data WHERE ip=:ip';
		} elseif ($status == 'delete') {
			$query = "DELETE FROM " . DB_PREFIX . "_attempts WHERE ip = ?";
		} elseif ($status == 'verify') {
			$query = "SELECT * FROM `" . DB_PREFIX . "_attempts` WHERE ip = ? LIMIT 1";
		} else {
			$connect = null;
			exit;
		}
		
		$result = $connect->prepare($query);
		$result->execute($array);
		$connect = null;
		
		if ($status == 'verify') {
			return $result->fetchAll(PDO::FETCH_ASSOC)[0];
		}
		
	}
}

// ПОЛЬЗОВАТЕЛИ - ЗАПИСЬ

// универсальная функция записи пользователя в базу данных

function saveUser($status, $array) {
	$connect = dbPDOConnect();
	
	if ($array['email']) {
		$array['email'] = clearData($array['email'], 'spaces code');
	}
	if ($array['phone']) {
		$array['phone'] = clearData($array['phone'], 'phone');
	}
	if ($array['password']) {
		$array['password'] = clearData($array['password'], 'spaces code');
	}
	if ($array['plan']) {
		$array['plan'] = clearData($array['plan'], 'spaces code realescape');
	}
	if ($array['verify']) {
		$array['verify'] = clearData($array['verify'], 'spaces realescape');
	}
	
	if ($status == 'registration') {
		
		$query = "INSERT INTO " . DB_PREFIX . "_registration(
			`id`, `email`, `phone`, `password`, `plan`, `date_register`, `verify`
		) VALUES(
			'" . uniqid() . "', ':email', ";
			if ($array['phone'] && $array['phone'] !== '') { $query .= "':phone', "; }
			if (!$array['phone'] || $array['phone'] == '') { $query .= "NULL, "; }
			$query .= "':password', ':plan', '" . date('Y-m-d H:i:s') . "', ':verify'
		)";
		
		$result = $connect->prepare($query);
		$result->execute($array);
		$connect = null;
		return $result;
		
	} elseif ($status == 'activation') {
		
		$query = "SELECT COUNT(id) FROM " . DB_PREFIX . "_registration WHERE `email`=':email' AND `verify`=':verify'";
		$result = $connect->prepare($query);
		$result->execute($array);
		$row = $result->fetch(PDO::FETCH_LAZY);
		
		if ($row['COUNT(id)'] != 1) { return; }
		
		$query = "INSERT INTO " . DB_PREFIX . "_users(
			`id`, `email`, `phone`, `password`, `plan`, `date_register`
		) SELECT 
			`id`, `email`, `phone`, `password`, `plan`, `date_register` 
		FROM " . DB_PREFIX . "_registration WHERE `email`=':email' AND `verify`=':verify'";
		
		$result = $connect->prepare($query);
		$result->execute($array);
		
		if ($result && $result == 1) {
			
			$query = "SELECT `id` FROM `" . DB_PREFIX . "_registration` WHERE `email` = ':email'";
			$result = $connect->prepare($query);
			$result->execute($array);
			
			while ($row = $result->fetch(PDO::FETCH_LAZY)) {
				$id = $row['id'];
			}
			
			if ($id) {
				createProjectDB('create', $id);
			}
			
		}
		
	} else {
		$connect = null;
		exit;
	}
	
	$connect = null;
	return $result;
}

function deleteUser($status, $array) {
	$connect = dbPDOConnect();
	
	if ($status == 'registration') {
		$query = "DELETE FROM `" . DB_PREFIX . "_registration` WHERE `email`=':email' AND `verify` = ':verify'";
	} else if ($status == 'deactivation') {
		$query = "DELETE FROM `" . DB_PREFIX . "_registration` WHERE `date_register` < '" . date('Y-m-d H:i:s', strtotime('-1 days')) . "'";
	} else {
		$connect = null;
		exit;
	}
	
	$result = $connect->prepare($query);
	$result->execute($array);
	
	$connect = null;
	return $result;
}

// универсальная функция поиска пользователя в базе данных по значению заданного поля
// ! не совсем универсальная, т.к. для множественного выбора приходится идти в 2 этапа:
//   - 1) делать выборку значений / $s = searchUser('search', 'id', 'plan', 0);
//   - 2) загружать данные по этим значениям / foreach ($s as $i) { $r = searchUser('load', '', 'id', $i); }

function searchUser($status, $field, $index, $value) {
	$connect = dbPDOConnect();
	$return = array();
	
	$array = array(
		'field' => $field,
		'index' => $index,
		'value' => $value
	);
	
	if ( is_string($array['value']) ) {
		$array['value'] = "'" . $array['value'] . "'";
	}
	
	if ($status == 'search' && ($field == 'password' || $field == 'id')) {
		$query = "SELECT `:field` FROM `" . DB_PREFIX . "_users` WHERE :index = :value";
	} elseif ($status == 'load' && $index == 'id') {
		$query = "SELECT * FROM `" . DB_PREFIX . "_users` WHERE :index = :value LIMIT 1";
	} else {
		$connect = null;
		exit;
	}
	
	$result = $connect->prepare($query);
	$result->execute($array);
	
	if ($status == 'search') {
		while ($row = $result->fetch(PDO::FETCH_LAZY)) {
			$return[] = $row[$field];
		}
	} elseif ($status == 'load') {
		while ($row = $result->fetch(PDO::FETCH_LAZY)) {
			$return = $row;
		}
	}
	
	$connect = null;
	return $return;
}

// универсальная функция перезаписи параметра пользователя в базе данных по id

function updateUser($status, $id, $parameter, $value) {
	$connect = dbPDOConnect();
	
	$array = array(
		'id' => $id,
		'parameter' => $parameter,
		'value' => $value
	);
	
	if ($status == 'update') {
		$query = "UPDATE `" . DB_PREFIX . "_users` SET `:parameter`=':value' WHERE `id`=':id'";
	} else {
		$connect = null;
		exit;
	}
	
	$result = $connect->prepare($query);
	$result->execute($array);
	
	$connect = null;
	
}

// КАРТОЧКИ - ЗАПИСЬ

// универсальная функция записи карточки в базу данных

function saveCard($status, $PID, $data) {
	$connect = dbPDOConnect();
	
	foreach ($data as $key => $item) {
		if ($key !== 'id' && $key !== 'text') {
			$data[$key] = clearData($item, 'realescape');			
		}
	}
	
	$id = (int)$data['id'];
	$content = json_decode($data['text'], true);
	foreach ($content as &$item) {
		$item['content'] = clearData($item['content'], 'html');
	}
	$data['text'] = json_encode($content);
	
	if ($status == 'savecard') {
		// если же имя базы является уникальным, то создается новая строка
		$query = "INSERT INTO " . $PID . "_cards(`id`, `title`, `marker`, `trash`, `text`) VALUES(" . $id . ", '" . $data['title'] . "', '" . $data['marker'] . "', '" . $data['trash'] . "', '" . $data['text'] . "')";
	} else if ($status == 'updatecard') {
		// если имя базы совпадает с уже имеющимся, то база обновляется
		$query = "UPDATE `" . $PID . "_cards` SET `title`='" . $data['title'] . "', `marker`='" . $data['marker'] . "', `trash`='" . $data['trash'] . "', `text`='" . $data['text'] . "' WHERE `id`=" . $id;
	} else {
		$connect = null;
		exit;
	}
	
	mysqli_query($connect, $query);
	$connect = null;
	
	echo $data['text'];
}

// универсальная функция поиска карточки в базе данных по id

function searchCard($status, $PID, $id) {
	$connect = dbPDOConnect();
	$id = (int)$id;
	
	if ($status == 'searchcard') {
		$query = "SELECT `id` FROM `" . $PID . "_cards` WHERE `id` = " . $id . " LIMIT 1";
	} else {
		$connect = null;
		exit;
	}
	
	$find = mysqli_query($connect, $query);
	$result = $find -> num_rows;
	$connect = null;
	
	return $result;
	
}

// универсальная функция перезаписи параметра карточки в базе данных по id

function refreshCard($status, $PID, $id, $parameter, $value) {
	$connect = dbPDOConnect();
	$id = (int)$id;
	$value = clearData($value, 'realescape');
	
	if ($status == 'refreshcard') {
		$query = "UPDATE `" . $PID . "_cards` SET `" . $parameter . "`='" . $value . "' WHERE `id`=" . $id;
	} else {
		$connect = null;
		exit;
	}
	
	mysqli_query($connect, $query);
	$connect = null;
	
}

// СТОЛЫ - ЗАПИСЬ

// универсальная функция записи стола и настроек в базу данных

function saveBoard($status, $PID, $name, $data) {
	$connect = dbPDOConnect();
	$name = clearData($name, 'realescape');
	$data = clearData($data, 'realescape');
	
	if ($status == 'saveboard') {
		$query = "UPDATE `" . $PID . "_boards` SET `content`='" . $data . "' WHERE `name`='" . $name . "'";
	} else {
		$connect = null;
		exit;
	}
	
	mysqli_query($connect, $query);
	$connect = null;
	
}

// ПРОЕКТЫ

// создание базы проектов для пользователя

function createProjectDB($status, $id) {
	$connect = dbPDOConnect();
	
	$id = clearData($id, 'realescape spaces code');
	
	/*
	$id = clearData($id, 'realescape');
	$id = clearData($id, 'spaces');
	$id = clearData($id, 'code');
	*/
	
	if ($status == 'create') {
		$query = "CREATE TABLE IF NOT EXISTS `" . $id . "_projects` (
			`id` varchar(255) NOT NULL,
			`title` varchar(255) NOT NULL,
			`description` mediumtext NOT NULL,
			`image` mediumtext NOT NULL,
			`status` varchar(8) NOT NULL,
			PRIMARY KEY (`id`),
			KEY `status` (`status`),
			KEY `title` (`title`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		";
	} else {
		$connect = null;
		exit;
	}
	
	mysqli_query($connect, $query);
	$connect = null;
	
}

// создание базы нового проекта

function createContentDB($status, $id) {
	$connect = dbPDOConnect();
	
	$id = clearData($id, 'realescape spaces code');
	
	/*
	$id = clearData($id, 'realescape');
	$id = clearData($id, 'spaces');
	$id = clearData($id, 'code');
	*/
	
	if ($status == 'create') {
		
		$query1 = "CREATE TABLE IF NOT EXISTS `" . $id . "_cards` (
			`id` int(11) NOT NULL,
			`title` varchar(255) NOT NULL,
			`marker` varchar(255) NOT NULL,
			`trash` varchar(255) NOT NULL,
			`text` mediumtext NOT NULL,
			PRIMARY KEY (`id`),
			KEY `title` (`title`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		";
		$query2 = "CREATE TABLE IF NOT EXISTS `" . $id . "_boards` (
			`id` int(11) NOT NULL,
			`name` varchar(255) NOT NULL,
			`content` mediumtext NOT NULL,
			PRIMARY KEY (`id`),
			UNIQUE KEY `type` (`name`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		";
		
		mysqli_query($connect, $query1);
		mysqli_query($connect, $query2);
		$connect = null;
		
	} elseif ($status == 'line') {
		
		$data = '[[0]]';
		$query = "INSERT INTO `" . $id . "_boards` (`id`, `name`, `content`) VALUES (1, 'line', '" . $data . "');";
		
		mysqli_query($connect, $query);
		$connect = null;
		
	} elseif ($status == 'map') {
		
		$data = '[{"id":0,"parent":""}]';
		$query = "INSERT INTO `" . $id . "_boards` (`id`, `name`, `content`) VALUES (2, 'map', '" . $data . "');";
		
		mysqli_query($connect, $query);
		$connect = null;
		
	} elseif ($status == 'settings') {
		
		$data = '{"columns":4,"orientation":0,"groupsize":5,"font":0,"bgimage":3,"bgcolor":"","bgtransparent":1,"cardtabs":1,"tabsorientation":0,"trashview":0,"cardview":0,"cardtrash":0,"cardsettings":0,"carddelete":0,"cardappend":0,"packtype":0,"trashtype":0}';
		$query = "INSERT INTO `" . $id . "_boards` (`id`, `name`, `content`) VALUES (0, 'settings', '" . $data . "');";
		
		mysqli_query($connect, $query);
		$connect = null;
		
	} else {
		$connect = null;
		exit;
	}
	
}

// создание нового проекта или редактирование настроек

function editProject($status, $id, $array) {
	$connect = dbPDOConnect();
	
	$id = clearData($id, 'realescape spaces code');
	
	/*
	$id = clearData($id, 'realescape');
	$id = clearData($id, 'spaces');
	$id = clearData($id, 'code');
	*/
	
	foreach ($array as $key => $item) {
		//$item = clearData($item, 'realescape');
		//$item = clearData($item, 'code');
		$item = clearData($item, 'realescape code');
		$array[$key] = $item;
	}
	
	if (!$array['title']) {
		$array['title'] = date('Y-m-d H:i');
	}
	
	$uniqid = uniqid();
	
	if ($status == 'create') {
		$query = "INSERT INTO " . $id . "_projects(`id`, `title`, `description`, `image`, `status`) VALUES('" . $uniqid . "', '" . $array['title'] . "', '" . $array['description'] . "', '" . $array['image'] . "', '" . $array['status'] . "')";
	} else if ($status == 'update') {
		$query = "UPDATE `" . $id . "_projects` SET `title`='" . $array['title'] . "', `description`='" . $array['description'] . "', ";
		if ($array['image']) {
			$query .= "`image`='" . trim($array['image']) . "', ";
		}
		$query .= "`status`='" . $array['status'] . "' WHERE `id`='" . $array['id'] . "'";
	} else {
		$connect = null;
		exit;
	}
	
	mysqli_query($connect, $query);
	$connect = null;
	
	return $uniqid;
}

// удаление проекта

function deleteProject($status, $id, $project) {
	$connect = dbPDOConnect();
	
	$id = clearData($id, 'realescape spaces code');
	
	/*
	$id = clearData($id, 'realescape');
	$id = clearData($id, 'spaces');
	$id = clearData($id, 'code');
	*/
	
	if ($status == 'delete') {
		$query = "UPDATE `" . $id . "_projects` SET `status`='delete' WHERE `id`='" . $project . "'";
	} else {
		$connect = null;
		exit;
	}
	
	mysqli_query($connect, $query);
	$connect = null;
	
}

// блокировка/разблокировка проекта

function lockProject($status, $id, $project) {
	$connect = dbPDOConnect();
	
	$id = clearData($id, 'realescape spaces code');
	
	/*
	$id = clearData($id, 'realescape');
	$id = clearData($id, 'spaces');
	$id = clearData($id, 'code');
	*/
	
	if ($status == 'lock') {
		$query = "UPDATE `" . $id . "_projects` SET `status`='lock' WHERE `id`='" . $project . "'";
	} elseif ($status == 'unlock') {
		$query = "UPDATE `" . $id . "_projects` SET `status`='' WHERE `id`='" . $project . "'";
	} elseif ($status == 'verify') {
		$query = "SELECT `status` FROM `" . $id . "_projects` WHERE `id`='" . $project . "'";
		
		$result = mysqli_query($connect, $query);
		$return = mysqli_fetch_assoc($result);
		$connect = null;
		return $return['status'];
		
	} else {
		$connect = null;
		exit;
	}
	
	mysqli_query($connect, $query);
	$connect = null;
	
}

// чтение проектов пользователя

function loadProjects($status, $id) {
	$connect = dbPDOConnect();
	
	$id = clearData($id, 'realescape spaces code');
	
	/*
	$id = clearData($id, 'realescape');
	$id = clearData($id, 'spaces');
	$id = clearData($id, 'code');
	*/
	
	if ($status == 'load') {
		$query = "SELECT * FROM `" . $id . "_projects` WHERE `status` != 'delete'";
	} else {
		$connect = null;
		exit;
	}
	
	$result = mysqli_query($connect, $query);
	if (!$result) { return;	}
	
	while($row = mysqli_fetch_assoc($result)){
		$return[] = $row;
	}
	if (!$return) { return;	}
	
	$connect = null;
	
	return $return;
	
}

// универсальная функция чтения проекта

function openProject($status, $id) {
	$connect = dbPDOConnect();
	$return = array(
		'cards' => array(),
		'line' => array(),
		'map' => array(),
		'settings' => array(),
	);
	
	if ($status == 'openproject') {
		$query1 = "SELECT * FROM `" . $id . "_cards`";
		$query2 = "SELECT `name`,`content` FROM `" . $id . "_boards`";
	} else {
		$connect = null;
		exit;
	}
	
	// запускаем цикл, в котором в пустой массив записываем запросы каждой строки из базы данных
	$result = array();
	
	$result = mysqli_query($connect, $query1);
	while($row = mysqli_fetch_assoc($result)){
		$return['cards'][] = $row;
	}
	$result = mysqli_query($connect, $query2);
	while($row = mysqli_fetch_assoc($result)){
		$data = clearData($row['content'], '');
		$return[$row['name']] = $data;
	}
	
	$connect = null;
	
	return $return;
}

?>