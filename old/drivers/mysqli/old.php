<?php

function dbConnect(){
	
	/*
	*  Функция подключения к базе данных стандартной библиотекой MySQLi
	*  на входе ничего указывать не нужно
	*
	*  функция берет все данные из констант и устанавливает соединение с базой данных
	*  в случае ошибки возвращает сообщение
	*/
	
	global $dberrors, $currlang;
	$connect = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME) or die( $dberrors -> connect -> $currlang . '<br>' . mysqli_connect_error() );
	mysqli_set_charset($connect, 'utf8') or die( $dberrors -> block . $dberrors -> charset -> $currlang );
	return $connect;
	
}

/* ФУНКЦИИ ДЛЯ РАБОТЫ С БАЗОЙ ДАННЫХ ПОЛЬЗОВАТЕЛЯ */

// ПОПЫТКИ АВТОРИЗАЦИИ

// универсальная функция записи попыток авторизации в базу данных

function dbAttempts($status, $array) {
	if (DB_TYPE && DB_TYPE !== 'nodb') {
		
		$connect = dbconnect();
		foreach ($array as &$item) {
			$item = clearData($item, 'realescape');
		}
		
		if ($status == 'save') {
			$query = "INSERT INTO " . DB_PREFIX . "_attempts(`ip`, `session`, `ban`, `counts`, `data`) VALUES('" . $array['ip'] . "', '" . $array['session'] . "', '" . $array['ban'] . "', '" . $array['counts'] . "', '" . $array['data'] . "')";
		} elseif ($status == 'update') {
			$query = "UPDATE `" . DB_PREFIX . "_attempts` SET `session`='" . $array['session'] . "', `ban`='" . $array['ban'] . "', `counts`='" . $array['counts'] . "', `data`='" . $array['data'] . "' WHERE `ip`='" . $array['ip'] . "'";
		} elseif ($status == 'delete') {
			$query = "DELETE FROM `" . DB_PREFIX . "_attempts` WHERE `ip` = '" . $array[0] . "'";
		} elseif ($status == 'verify') {
			$query = "SELECT * FROM `" . DB_PREFIX . "_attempts` WHERE `ip` = '" . $array[0] . "' LIMIT 1";
		} else {
			mysqli_close($connect);
			exit;
		}
		
		if ($status == 'verify') {
			$result = mysqli_query($connect, $query);
			$return = array();
			while ($row = mysqli_fetch_assoc($result)) {
				$return = $row;
			}
			mysqli_close($connect);
			return $return;
		} else {
			mysqli_query($connect, $query);
			mysqli_close($connect);
		}
		
	}
}

// ПОЛЬЗОВАТЕЛИ - ЗАПИСЬ

// универсальная функция записи пользователя в базу данных

function saveUser($status, $array) {
	$connect = dbconnect();
	
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
			'" . uniqid() . "', '" . $array['email'] . "', ";
			if ($array['phone'] && $array['phone'] !== '') { $query .= "'" . $array['phone'] . "', "; }
			if (!$array['phone'] || $array['phone'] == '') { $query .= "NULL, "; }
			$query .= "'" . $array['password'] . "', '" . $array['plan'] . "', '" . date('Y-m-d H:i:s') . "', '" . $array['verify'] . "'
		)";
		
		$result = mysqli_query($connect, $query);
		mysqli_close($connect);
		return $result;
		
	} else if ($status == 'activation') {
		
		global $dberrors, $currlang;
		
		$query = "SELECT COUNT(id) FROM " . DB_PREFIX . "_registration WHERE `email`='" . $array['email'] . "' AND `verify`='" . $array['verify'] . "'";
		$result = mysqli_query($connect, $query) or die( $dberrors -> query -> $currlang );
		$row = mysqli_fetch_assoc($result);
		if ($row['COUNT(id)'] != 1) { return; }
		
		$query = "INSERT INTO " . DB_PREFIX . "_users(
			`id`, `email`, `phone`, `password`, `plan`, `date_register`
		) SELECT 
			`id`, `email`, `phone`, `password`, `plan`, `date_register` 
		FROM " . DB_PREFIX . "_registration WHERE `email`='" . $array['email'] . "' AND `verify`='" . $array['verify'] . "'";
		
		$result = mysqli_query($connect, $query);
		
		if ($result && $result == 1) {
			
			$query = "SELECT `id` FROM `" . DB_PREFIX . "_registration` WHERE `email` = '" . $array['email'] . "'";
			$result = mysqli_query($connect, $query) or die( $dberrors -> query -> $currlang );
			
			while ($row = mysqli_fetch_assoc($result)) {
				$id = $row['id'];
			}
			
			if ($id) {
				createProjectDB('create', $id);
			}
			
		}
		
	} else {
		mysqli_close($connect);
		exit;
	}
	
	mysqli_close($connect);
	return $result;
}

function deleteUser($status, $array) {
	$connect = dbconnect();
	
	if ($status == 'registration') {
		$query = "DELETE FROM `" . DB_PREFIX . "_registration` WHERE `email`='" . $array['email'] . "' AND `verify` = '" . $array['verify'] . "'";
	} else if ($status == 'deactivation') {
		$query = "DELETE FROM `" . DB_PREFIX . "_registration` WHERE `date_register` < '" . date('Y-m-d H:i:s', strtotime('-1 days')) . "'";
	} else {
		mysqli_close($connect);
		exit;
	}
	
	$result = mysqli_query($connect, $query);
	mysqli_close($connect);
	return $result;
}

// универсальная функция поиска пользователя в базе данных по значению заданного поля
// ! не совсем универсальная, т.к. для множественного выбора приходится идти в 2 этапа:
//   - 1) делать выборку значений / $s = searchUser('search', 'id', 'plan', 0);
//   - 2) загружать данные по этим значениям / foreach ($s as $i) { $r = searchUser('load', '', 'id', $i); }

function searchUser($status, $field, $index, $value) {
	$connect = dbconnect();
	$return = array();
	$field = clearData($field, 'realescape');
	$index = clearData($index, 'realescape');
	$value = clearData($value, 'realescape');
	
	if ( is_string($value) ) {
		$value = "'" . $value . "'";
	}
	
	if ($status == 'search' && ($field == 'password' || $field == 'id')) {
		$query = "SELECT `" . $field . "` FROM `" . DB_PREFIX . "_users` WHERE `" . $index . "` = " . $value;
	} elseif ($status == 'load' && $index == 'id') {
		$query = "SELECT * FROM `" . DB_PREFIX . "_users` WHERE `" . $index . "` = " . $value . " LIMIT 1";
	} else {
		mysqli_close($connect);
		exit;
	}
	
	$result = mysqli_query($connect, $query);
	
	if ($status == 'search') {
		while ($row = mysqli_fetch_assoc($result)) {
			$return[] = $row[$field];
		}
	} elseif ($status == 'load') {
		while ($row = mysqli_fetch_assoc($result)) {
			$return = $row;
		}
	}
	
	mysqli_close($connect);
	return $return;
}

// универсальная функция перезаписи параметра пользователя в базе данных по id

function updateUser($status, $id, $parameter, $value) {
	$connect = dbconnect();
	$id = clearData($id, 'realescape');
	$parameter = clearData($parameter, 'realescape');
	$value = clearData($value, 'realescape');
	
	if ($status == 'update') {
		$query = "UPDATE `" . DB_PREFIX . "_users` SET `" . $parameter . "`='" . $value . "' WHERE `id`='" . $id . "'";
	} else {
		mysqli_close($connect);
		exit;
	}
	
	mysqli_query($connect, $query);
	mysqli_close($connect);
	
}

// КАРТОЧКИ - ЗАПИСЬ

// универсальная функция записи карточки в базу данных

function saveCard($status, $PID, $data) {
	global $dberrors, $currlang;
	$connect = dbconnect();
	
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
		mysqli_close($connect);
		exit;
	}
	
	mysqli_query($connect, $query) or die( $dberrors -> query -> $currlang );
	mysqli_close($connect);
	
	echo $data['text'];
}

// универсальная функция поиска карточки в базе данных по id

function searchCard($status, $PID, $id) {
	$connect = dbconnect();
	$id = (int)$id;
	
	if ($status == 'searchcard') {
		$query = "SELECT `id` FROM `" . $PID . "_cards` WHERE `id` = " . $id . " LIMIT 1";
	} else {
		mysqli_close($connect);
		exit;
	}
	
	$find = mysqli_query($connect, $query);
	$result = $find -> num_rows;
	mysqli_close($connect);
	
	return $result;
	
}

// универсальная функция перезаписи параметра карточки в базе данных по id

function refreshCard($status, $PID, $id, $parameter, $value) {
	$connect = dbconnect();
	$id = (int)$id;
	$value = clearData($value, 'realescape');
	
	if ($status == 'refreshcard') {
		$query = "UPDATE `" . $PID . "_cards` SET `" . $parameter . "`='" . $value . "' WHERE `id`=" . $id;
	} else {
		mysqli_close($connect);
		exit;
	}
	
	mysqli_query($connect, $query);
	mysqli_close($connect);
	
}

// СТОЛЫ - ЗАПИСЬ

// универсальная функция записи стола и настроек в базу данных

function saveBoard($status, $PID, $name, $data) {
	$connect = dbconnect();
	$name = clearData($name, 'realescape');
	$data = clearData($data, 'realescape');
	
	if ($status == 'saveboard') {
		$query = "UPDATE `" . $PID . "_boards` SET `content`='" . $data . "' WHERE `name`='" . $name . "'";
	} else {
		mysqli_close($connect);
		exit;
	}
	
	mysqli_query($connect, $query);
	mysqli_close($connect);
	
}

// ПРОЕКТЫ

// создание базы проектов для пользователя

function createProjectDB($status, $id) {
	global $dberrors, $currlang;
	$connect = dbconnect();
	
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
		mysqli_close($connect);
		exit;
	}
	
	mysqli_query($connect, $query) or die( $dberrors -> query -> $currlang );
	mysqli_close($connect);
	
}

// создание базы нового проекта

function createContentDB($status, $id) {
	global $dberrors, $currlang;
	$connect = dbconnect();
	
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
		
		mysqli_query($connect, $query1) or die( $dberrors -> query -> $currlang );
		mysqli_query($connect, $query2) or die( $dberrors -> query -> $currlang );
		mysqli_close($connect);
		
	} elseif ($status == 'line') {
		
		$data = '[[0]]';
		$query = "INSERT INTO `" . $id . "_boards` (`id`, `name`, `content`) VALUES (1, 'line', '" . $data . "');";
		
		mysqli_query($connect, $query) or die( $dberrors -> query -> $currlang );
		mysqli_close($connect);
		
	} elseif ($status == 'map') {
		
		$data = '[{"id":0,"parent":""}]';
		$query = "INSERT INTO `" . $id . "_boards` (`id`, `name`, `content`) VALUES (2, 'map', '" . $data . "');";
		
		mysqli_query($connect, $query) or die( $dberrors -> query -> $currlang );
		mysqli_close($connect);
		
	} elseif ($status == 'settings') {
		
		$data = '{"columns":4,"orientation":0,"groupsize":5,"font":0,"bgimage":3,"bgcolor":"","bgtransparent":1,"cardtabs":1,"tabsorientation":0,"trashview":0,"cardview":0,"cardtrash":0,"cardsettings":0,"carddelete":0,"cardappend":0,"packtype":0,"trashtype":0}';
		$query = "INSERT INTO `" . $id . "_boards` (`id`, `name`, `content`) VALUES (0, 'settings', '" . $data . "');";
		
		mysqli_query($connect, $query) or die( $dberrors -> query -> $currlang );
		mysqli_close($connect);
		
	} else {
		mysqli_close($connect);
		exit;
	}
	
}

// создание нового проекта или редактирование настроек

function editProject($status, $id, $array) {
	global $dberrors, $currlang;
	$connect = dbconnect();
	
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
		mysqli_close($connect);
		exit;
	}
	
	mysqli_query($connect, $query) or die( $dberrors -> query -> $currlang );
	mysqli_close($connect);
	
	return $uniqid;
}

// удаление проекта

function deleteProject($status, $id, $project) {
	global $dberrors, $currlang;
	$connect = dbconnect();
	
	$id = clearData($id, 'realescape spaces code');
	
	/*
	$id = clearData($id, 'realescape');
	$id = clearData($id, 'spaces');
	$id = clearData($id, 'code');
	*/
	
	if ($status == 'delete') {
		$query = "UPDATE `" . $id . "_projects` SET `status`='delete' WHERE `id`='" . $project . "'";
	} else {
		mysqli_close($connect);
		exit;
	}
	
	mysqli_query($connect, $query) or die( $dberrors -> query -> $currlang );
	mysqli_close($connect);
	
}

// блокировка/разблокировка проекта

function lockProject($status, $id, $project) {
	global $dberrors, $currlang;
	$connect = dbconnect();
	
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
		mysqli_close($connect);
		return $return['status'];
		
	} else {
		mysqli_close($connect);
		exit;
	}
	
	mysqli_query($connect, $query) or die( $dberrors -> query -> $currlang );
	mysqli_close($connect);
	
}

// чтение проектов пользователя

function loadProjects($status, $id) {
	$connect = dbconnect();
	
	$id = clearData($id, 'realescape spaces code');
	
	/*
	$id = clearData($id, 'realescape');
	$id = clearData($id, 'spaces');
	$id = clearData($id, 'code');
	*/
	
	if ($status == 'load') {
		$query = "SELECT * FROM `" . $id . "_projects` WHERE `status` != 'delete'";
	} else {
		mysqli_close($connect);
		exit;
	}
	
	$result = mysqli_query($connect, $query);
	if (!$result) { return;	}
	
	while($row = mysqli_fetch_assoc($result)){
		$return[] = $row;
	}
	if (!$return) { return;	}
	
	mysqli_close($connect);
	
	return $return;
	
}

// универсальная функция чтения проекта

function openProject($status, $id) {
	$connect = dbconnect();
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
		mysqli_close($connect);
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
	
	mysqli_close($connect);
	
	return $return;
}

?>