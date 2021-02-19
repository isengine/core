<?php defined('isENGINE') or die;

// большой блок по управлению правами
// здесь мы должны узнать, куда стучится пользователь и разрешено ли ему переходить на эту страничку
// т.е. разобраться с правами пользователя, с доступом к тем или иным разделам сайта,
// для чего сперва узнать, был ли запрос в админку и разобрать структуру
// ну и загрузить в структуру или убрать из нее нужный раздел сайта
// после чего переключиться на роутер и он уже покажет -
// если, например, в структуре такого раздела или такой страницы нет, то все - ошибка
// но в каком порядке выполнять эти пункты - надо разобраться

// здесь есть два варианта: либо мы берем все записи и читаем сразу
// либо мы проверяем каждый раз при каком-либо действии с базой данных

// первый вариант проще в реализации и меньше загружает память, но вопрос с безопасностью -
// возможно ли будет тогда подделать запрос, изменив входные данные пользователя?

// второй вариант сложнее, он меньше грузит систему вначале, но сильно загружает систему
// при каждом запросе, т.к. при каждом запросе будет читаться содержимое базы прав,
// зато нет проблем с безопасностью

// теперь вместо поля rights - поле self, которое разбирается по-умолчанию как массив
// указывать его после точки в имени файла, как обычно, а значения массива разделять через пробел
// это будут имена пользователей, которые считаются авторами

// по-умолчанию автор или будет один, или его вообще не будет
// но каждый, кому разрешено редактировать данные, получает доступ и к редактированию имени/типа/родителя/self
// таким образом каждый автор сможет добавить себе соавтора

global $user;

if (SECURE_RIGHTS) {
	
	if (
		isALLOW &&
		!empty($_SESSION['rights']) &&
		cookie('rights', true)
	) {
		
		if ($_SESSION['rights'] === md5(cookie('rights', true))) {
			$user -> rights = json_decode(cookie('rights', true), true);
		} else {
			userUnset();
			error('403', false, 'rights for \'' . $user -> name . '\' user not match hash - may be is hack');
		}
		
	} else {
		
		// читаем права пользователя
		
		$rightsgroup = objectIs($user -> parent) && isALLOW ? array_pop($user -> parent) : 'default';
		$usersrights = dbUse('rights:' . $rightsgroup, 'select', ['return' => 'alone:data']);
		
		if (!empty($usersrights)) {
			
			$defaultrights = [
				'read' => true,
				'write' => USERS_RIGHTS,
				'create' => USERS_RIGHTS,
				'delete' => USERS_RIGHTS
			];
			
			// формируем список полей
			
			foreach ($usersrights as &$item) {
				
				$item = array_merge($defaultrights, $item);
				
				foreach ($item as $k => &$i) {
					
					if (is_array($i)) {
						
						if (!isset($i['allow'])) {
							$i['allow'] = USERS_RIGHTS;
						}
						if (!isset($i['self'])) {
							$i['self'] = USERS_RIGHTS;
						}
						
						foreach (['allow', 'deny', 'self', 'exclude'] as $ii) {
							if (!empty($i[$ii]['fields']) && is_array($i[$ii]['fields'])) {
								$i['fields'][$ii] = $i[$ii]['fields'];
								unset($i[$ii]['fields']);
							}
						}
						unset($ii);
						
					}
					
				}
			}
			
			unset($item, $i, $k, $defaultrights);
			
		} elseif (
			isALLOW && LOG_MODE === 'warning' ||
			LOG_MODE === 'panic'
		) {
			logging('rules for \'' . $rightsgroup . '\' rights group (as parent of user) not set in database');
		}
		
		// записываем в сессию и куки
		
		$_SESSION['rights'] = md5(json_encode($usersrights));
		cookie('rights', json_encode($usersrights));
		$user -> rights = $usersrights;
		
		unset($usersrights, $rightsgroup);
		
	}
	
}

?>