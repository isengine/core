<?php

/* ФУНКЦИИ ДЛЯ РАБОТЫ С БАЗОЙ ДАННЫХ ПОЛЬЗОВАТЕЛЯ */

// ПОПЫТКИ АВТОРИЗАЦИИ

// универсальная функция записи попыток авторизации в базу данных

function dbAttempts($status, $array) {
	if (DB_TYPE && DB_TYPE !== 'nodb') {
		
		global $db;
		
		if ($status == 'save') {
			$query = "INSERT INTO " . DB_PREFIX . "_attempts(`ip`, `session`, `ban`, `counts`, `data`) VALUES('" . $array['ip'] . "', '" . $array['session'] . "', '" . $array['ban'] . "', '" . $array['counts'] . "', '" . $array['data'] . "')";
		} elseif ($status == 'update') {
			$query = "UPDATE `" . DB_PREFIX . "_attempts` SET `session`='" . $array['session'] . "', `ban`='" . $array['ban'] . "', `counts`='" . $array['counts'] . "', `data`='" . $array['data'] . "' WHERE `ip`='" . $array['ip'] . "'";
		} elseif ($status == 'delete') {
			$query = "DELETE FROM `" . DB_PREFIX . "_attempts` WHERE `ip` = '" . $array[0] . "'";
		} elseif ($status == 'verify') {
			$query = "SELECT * FROM `" . DB_PREFIX . "_attempts` WHERE `ip` = '" . $array[0] . "' LIMIT 1";
		} else {
			$db = null;
			exit;
		}
		
		if ($status == 'verify') {
			$result = $db->query($query);
			$return = array();
			while ($row = $result->fetchAll()){
				$return = $row;
			}
			return $return;
		} else {
			$db->query($query);
		}
		
	}
}

// ПОЛЬЗОВАТЕЛИ - ЗАПИСЬ

// универсальная функция записи пользователя в базу данных

function saveUser($status, $array) {
	
	global $db;
	
	if ($status == 'activation') {
		
		if ($array['email']) {
			$array['email'] = clear($array['email'], 'nospaces code');
		}
		if ($array['verify']) {
			$array['verify'] = clear($array['verify'], 'nospaces');
		}
		
		$query = "SELECT COUNT(id) FROM " . DB_PREFIX . "_registration WHERE `email`='" . $array['email'] . "' AND `verify`='" . $array['verify'] . "'";
		
		$result = $db->query($query);
		$row = $result->fetch(PDO::FETCH_LAZY);
		if ($row['COUNT(id)'] != 1) { return; }
		
		$query = "INSERT INTO " . DB_PREFIX . "_users(
			`id`, `email`, `phone`, `password`, `plan`, `date_register`
		) SELECT 
			`id`, `email`, `phone`, `password`, `plan`, `date_register` 
		FROM " . DB_PREFIX . "_registration WHERE `email`='" . $array['email'] . "' AND `verify`='" . $array['verify'] . "'";
		
		$result = $db->query($query);
		
		if ($result && $result == 1) {
			
			$query = "SELECT `id` FROM `" . DB_PREFIX . "_registration` WHERE `email` = '" . $array['email'] . "'";
			$result = $db->query($query);
			
			while ($row = $result->fetch(PDO::FETCH_LAZY)) {
				$id = $row['id'];
			}
			
			if ($id) {
				createProjectDB('create', $id);
			}
			
		}
		
	} else {
		$db = null;
		exit;
	}
	
	return $result;
}

// объединение dbUser > deleteUser + updateUser + searchUser + saveUser

// универсальная функция удаления пользователя (пока только из списка регистраций, при переносе в список users, при регистрации)

// универсальная функция перезаписи параметра пользователя в базе данных по id

// универсальная функция поиска пользователя в базе данных по значению заданного поля
// ! не совсем универсальная, т.к. для множественного выбора приходится идти в 2 этапа:
//   - 1) делать выборку значений / $s = searchUser('search', 'id', 'plan', 0);
//   - 2) загружать данные по этим значениям / foreach ($s as $i) { $r = searchUser('load', '', 'id', $i); }

function dbUser($status, $array) {
	
	global $db;
	
	$clear = array('id', 'email', 'phone', 'password', 'plan', 'verify', 'field', 'index', 'parameter', 'value');
	
	foreach ($array as $key => &$item) {
		if (in_array($key, $clear)) {
			if ($key === 'phone') {
				$item = clear($item, 'nospaces phone');
			} else {
				$item = clear($item, 'nospaces code');
			}
		}
	}
	
	if ($status == 'delregistration') {
		
		$query = "DELETE FROM `" . DB_PREFIX . "_registration` WHERE `email`='" . $array['email'] . "' AND `verify` = '" . $array['verify'] . "'";
		
	} elseif ($status == 'deactivation') {
		
		$query = "DELETE FROM `" . DB_PREFIX . "_registration` WHERE `date_register` < '" . date('Y-m-d H:i:s', strtotime('-1 days')) . "'";
		
	} elseif ($status == 'update') {
		
		$query = "UPDATE `" . DB_PREFIX . "_users` SET `" . $array['parameter'] . "`='" . $array['value'] . "' WHERE `id`='" . $array['id'] . "'";
		
	} elseif ($status == 'search' && ($array['field'] == 'password' || $array['field'] == 'id')) {

		if (is_string($array['value'])) { $array['value'] = "'" . $array['value'] . "'"; }
		$query = "SELECT `" . $array['field'] . "` FROM `" . DB_PREFIX . "_users` WHERE `" . $array['index'] . "` = " . $array['value'];
		
	} elseif ($status == 'load') {
		
		if (is_string($array['value'])) { $array['value'] = "'" . $array['value'] . "'"; }
		$query = "SELECT * FROM `" . DB_PREFIX . "_users` WHERE `id` = " . $array['value'] . " LIMIT 1";
		
	} elseif ($status == 'registration') {
		
		$query = "INSERT INTO " . DB_PREFIX . "_registration(
			`id`, `email`, `phone`, `password`, `plan`, `date_register`, `verify`
		) VALUES(
			'" . uniqid() . "', '" . $array['email'] . "', ";
			if ($array['phone'] && $array['phone'] !== '') { $query .= "'" . $array['phone'] . "'"; } else { $query .= "NULL"; }
			$query .= ", '" . $array['password'] . "', '" . $array['plan'] . "', '" . date('Y-m-d H:i:s') . "', '" . $array['verify'] . "'
		)";
		
	} elseif ($status == 'activation') {
		
		$query = "INSERT INTO " . DB_PREFIX . "_users(
			`id`, `email`, `phone`, `password`, `plan`, `date_register`
		) SELECT 
			`id`, `email`, `phone`, `password`, `plan`, `date_register` 
		FROM " . DB_PREFIX . "_registration WHERE `email`='" . $array['email'] . "' AND `verify`='" . $array['verify'] . "'";
		
	} else {
		$db = null;
		exit;
	}
	
	$result = $db->query($query);
	
	if ($status == 'search' || $status == 'load') {
		
		$return = array();
		if ($status == 'search') {
			while ($row = $result->fetch(PDO::FETCH_LAZY)) {
				$return[] = $row[$array['field']];
			}
		} else {
			while ($row = $result->fetch(PDO::FETCH_LAZY)) {
				$return = $row;
			}
		}
		return $return;
		
	}
	
	return $result;
}


// КАРТОЧКИ - ЗАПИСЬ

// универсальная функция записи карточки в базу данных

function saveCard($status, $PID, $data) {
	
	global $db;
	
	foreach ($data as $key => $item) {
		if ($key !== 'id' && $key !== 'text') {
			//$data[$key] = clear($item, 'code');
		}
	}
	
	$id = (int)$data['id'];
	$content = json_decode($data['text'], true);
	foreach ($content as &$item) {
		$item['content'] = clear($item['content'], 'html');
	}
	$data['text'] = json_encode($content);
	
	if ($status == 'savecard') {
		// если же имя базы является уникальным, то создается новая строка
		$query = "INSERT INTO " . $PID . "_cards(`id`, `title`, `marker`, `trash`, `text`) VALUES(" . $id . ", '" . $data['title'] . "', '" . $data['marker'] . "', '" . $data['trash'] . "', '" . $data['text'] . "')";
	} elseif ($status == 'updatecard') {
		// если имя базы совпадает с уже имеющимся, то база обновляется
		$query = "UPDATE `" . $PID . "_cards` SET `title`='" . $data['title'] . "', `marker`='" . $data['marker'] . "', `trash`='" . $data['trash'] . "', `text`='" . $data['text'] . "' WHERE `id`=" . $id;
	} else {
		$db = null;
		exit;
	}
	
	$db->query($query);
	
	echo $data['text'];
}

// универсальная функция поиска карточки в базе данных по id

function searchCard($status, $PID, $id) {
	global $db;
	$id = (int)$id;
	
	if ($status == 'searchcard') {
		$query = "SELECT `id` FROM `" . $PID . "_cards` WHERE `id` = " . $id . " LIMIT 1";
	} else {
		$db = null;
		exit;
	}
	
	$find = $db->query($query);
	$result = $find -> num_rows;
	
	return $result;
	
}

// универсальная функция перезаписи параметра карточки в базе данных по id

function refreshCard($status, $PID, $id, $parameter, $value) {
	global $db;
	$id = (int)$id;
	
	if ($status == 'refreshcard') {
		$query = "UPDATE `" . $PID . "_cards` SET `" . $parameter . "`='" . $value . "' WHERE `id`=" . $id;
	} else {
		$db = null;
		exit;
	}
	
	$db->query($query);
	
}

// СТОЛЫ - ЗАПИСЬ

// универсальная функция записи стола и настроек в базу данных

function saveBoard($status, $PID, $name, $data) {
	global $db;
	
	if ($status == 'saveboard') {
		$query = "UPDATE `" . $PID . "_boards` SET `content`='" . $data . "' WHERE `name`='" . $name . "'";
	} else {
		$db = null;
		exit;
	}
	
	$db->query($query);
	
}

// ПРОЕКТЫ

// создание базы проектов для пользователя

function createProjectDB($status, $id) {
	
	global $db;
	
	$id = clear($id, 'nospaces code');
	
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
		$db = null;
		exit;
	}
	
	$db->query($query);
	
}

// создание базы нового проекта

function createContentDB($status, $id) {
	
	global $db;
	
	$id = clear($id, 'nospaces code');
	
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
		
		$db->query($query1);
		$db->query($query2);
		
	} elseif ($status == 'line') {
		
		$data = '[[0]]';
		$query = "INSERT INTO `" . $id . "_boards` (`id`, `name`, `content`) VALUES (1, 'line', '" . $data . "');";
		
		$db->query($query);
		
	} elseif ($status == 'map') {
		
		$data = '[{"id":0,"parent":""}]';
		$query = "INSERT INTO `" . $id . "_boards` (`id`, `name`, `content`) VALUES (2, 'map', '" . $data . "');";
		
		$db->query($query);
		
	} elseif ($status == 'settings') {
		
		$data = '{"columns":4,"orientation":0,"groupsize":5,"font":0,"bgimage":3,"bgcolor":"","bgtransparent":1,"cardtabs":1,"tabsorientation":0,"trashview":0,"cardview":0,"cardtrash":0,"cardsettings":0,"carddelete":0,"cardappend":0,"packtype":0,"trashtype":0}';
		$query = "INSERT INTO `" . $id . "_boards` (`id`, `name`, `content`) VALUES (0, 'settings', '" . $data . "');";
		
		$db->query($query);
		
	} else {
		$db = null;
		exit;
	}
	
}

// создание нового проекта или редактирование настроек

function editProject($status, $id, $array) {
	
	global $db;
	
	$id = clear($id, 'nospaces code');
	
	foreach ($array as $key => $item) {
		$item = clear($item, 'code');
		$array[$key] = $item;
	}
	
	if (!$array['title']) {
		$array['title'] = date('Y-m-d H:i');
	}
	
	$uniqid = uniqid();
	
	if ($status == 'create') {
		$query = "INSERT INTO " . $id . "_projects(`id`, `title`, `description`, `image`, `status`) VALUES('" . $uniqid . "', '" . $array['title'] . "', '" . $array['description'] . "', '" . $array['image'] . "', '" . $array['status'] . "')";
	} elseif ($status == 'update') {
		$query = "UPDATE `" . $id . "_projects` SET `title`='" . $array['title'] . "', `description`='" . $array['description'] . "', ";
		if ($array['image']) {
			$query .= "`image`='" . trim($array['image']) . "', ";
		}
		$query .= "`status`='" . $array['status'] . "' WHERE `id`='" . $array['id'] . "'";
	} else {
		$db = null;
		exit;
	}
	
	$db->query($query);
	
	return $uniqid;
}

// удаление проекта

function deleteProject($status, $id, $project) {
	
	global $db;
	
	$id = clear($id, 'nospaces code');
	
	if ($status == 'delete') {
		$query = "UPDATE `" . $id . "_projects` SET `status`='delete' WHERE `id`='" . $project . "'";
	} else {
		$db = null;
		exit;
	}
	
	$db->query($query);
	
}

// блокировка/разблокировка проекта

function lockProject($status, $id, $project) {
	
	global $db;
	
	$id = clear($id, 'nospaces code');
	
	if ($status == 'lock') {
		$query = "UPDATE `" . $id . "_projects` SET `status`='lock' WHERE `id`='" . $project . "'";
	} elseif ($status == 'unlock') {
		$query = "UPDATE `" . $id . "_projects` SET `status`='' WHERE `id`='" . $project . "'";
	} elseif ($status == 'verify') {
		$query = "SELECT `status` FROM `" . $id . "_projects` WHERE `id`='" . $project . "'";
		
		$result = $db->query($query);
		$return = $db->fetch($result);
		return $return['status'];
		
	} else {
		$db = null;
		exit;
	}
	
	$db->query($query);
	
}

// чтение проектов пользователя

function loadProjects($status, $id) {
	global $db;
	
	$id = clear($id, 'nospaces code');
	
	if ($status == 'load') {
		$query = "SELECT * FROM `" . $id . "_projects` WHERE `status` != 'delete'";
	} else {
		$db = null;
		exit;
	}
	
	$result = $db->query($query);
	if (!$result) { return;	}
	
	while($row = $db->fetch($result)){
		$return[] = $row;
	}
	if (!$return) { return;	}
	
	return $return;
	
}

// универсальная функция чтения проекта

function openProject($status, $id) {
	global $db;
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
		$db = null;
		exit;
	}
	
	// запускаем цикл, в котором в пустой массив записываем запросы каждой строки из базы данных
	$result = array();
	
	$result = $db->query($query1);
	while($row = $db->fetch($result)){
		$return['cards'][] = $row;
	}
	$result = $db->query($query2);
	while($row = $db->fetch($result)){
		$data = clear($row['content'], '');
		$return[$row['name']] = $data;
	}
	
	return $return;
}

?>