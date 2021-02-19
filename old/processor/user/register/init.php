<?php defined('isPROCESS') or die;

/*
мы проверяем, есть ли такое имя пользователя в базе данных
вполне возможно, что имя пользователя имеет смысл формировать как хэш
т.е. не 'user', а 'x897fgj340sdf'

если такого имени в базе нет, мы записываем пользователя в базу данных
с каким-нибудь типом, который указывает на то, что пользователь не подтвержден.
соответственно, в дальнейшем, такой пользователь не сможет выполнять определенные действия,
пока он не подтвердит свою запись.

подтверждение записи в стандартном случае проходит через email, ну или через спец.форму.
Для пользователя генерируется токен, ключ, который требует подтверждения, и отправляется по email.
Также он записывается куда-нибудь, например, в поле 'type', т.к. оно для пользователей не имеет смысла вообще,
ну и так можно его хоть чем-нибудь загрузить.

В поле записывается ключ, а пользователю отправляется для подтверждения хэщ, ну или наоборот.
В любом случае, результат один - специальным запросом ключ и хэш проверяются на совпадение и после этого
неподтвержденный статус пользователя снимается.

Какая здесь защита от взлома?
Ну, вы можете подтвердить пользователя только в том случае, если знаете его имя и ключ. Причем лучше знать именно ключ.
Потому что если вы прочитаете базу, там окажется хэш, который вы не сможете декодировать.


нам нужны еще несколько типов полей
- текущая дата/время в абсолютном или заданном формате
- агент/ip есть, но надо сделать заполнение
- поле в виде массива с добавлением значений и возможностью задать ограниченный размер массива,
  при этом если идет запись, но массив заполнен, первое значение стирается (массив смещается как стек)
- поле, участвующее при регистрации, но необязательное
  (возможно, в этом нет никакого смысла, т.к. такие поля заполняются потом, а потом шаблон настраивается вручную)
- confirm поле, участвующее в процедуре подтверждения, но необязательное
  id берется из заполненного поля,
  тип и прочие параметры передаются в функцию send напрямую
  так что по всем параметрам поля см.функцию send, а задавать confirm нужно в базе полей пользователя
  но для подтверждения также нужно задавать настройки регистрации в настройках системы

кстати...

на момент 'process' уже загружены функции:
	ini
	data
	object

*/

global $userstable;
global $user;

$form = $process -> data;
$fields = dbUse($userstable, 'filter', ['filter' => 'required system:password:authorise', 'or' => true]);
$login = null;
$result = null;
$send = [];
$errors = [];

foreach ($fields as $item) {
	
	$name = $item['name'];
	$data = $item['data'];
	
	// здесь 'name' - это имя поля, по которому мы будем искать его в массиве 'form'
	// массив 'form' - это массив переданных через форму данных, которые нам нужно проверить
	// ну а 'data' - это массив настроек пользовательских данных, откуда мы берем правила проверки и обработки
	
	if (!empty($data['clear'])) {
		$form[$name] = clear($form[$name], $data['clear']);
	}
	
	if (
		!empty($data['validation']) ||
		!empty($data['rules'])
	) {
		
		// если мы обнаружили поле с правилами проверки
		// мы должны обработать результат и сравнить с изначальным
		
		$validation = clear($form[$name], set($data['validation'], true), true, set($data['rules'], true));
		
		if ($form[$name] !== $validation) {
			$errors[] = 'validation ' . $name;
		}
		
		unset($validation);
		
	}
	
	if (objectIs($data['confirm']) && !empty($form[$name])) {
		
		// поля для подтверждения и активации пользователя
		
		$send[] = [
			'subject' => $data['confirm']['subject'], // 'регистрация на сайте'
			'message' => $data['confirm']['message'], // 'код подтверждения: {confirm}'
			'data' => [
				'template' => $data['confirm']['template'],
				'type' => $data['confirm']['type'],
				'key' => !empty(json_decode($data['confirm']['key'])) ? json_decode($data['confirm']['key']) : $data['confirm']['key'],
				'param' => !empty(json_decode($data['confirm']['param'])) ? json_decode($data['confirm']['param']) : $data['confirm']['param'],
				'id' => $form[$name]
			]
		];
		
	}
	
	if (!empty($data['system'])) {
		
		// если мы обнаружили системное поле
		// то здесь будут действовать определенные правила
		
		if ($data['system'] === 'password' && set($form[$name])) {
			
			// если мы обнаружили поле пароля
			// мы должны сверить поля из формы и захэшировать результат, согласно правилам
			// проверка заключается в том, что должно присутствовать повторное поле с тем же значением
			// правила обработки результата - это криптование, хэширование, либо оставить как есть
			
			if (!empty($form[$name]) && !empty($form[$name . '-repeat']) && $form[$name] === $form[$name . '-repeat']) {
				unset($form[$name . '-repeat']);
			} else {
				$errors[] = 'password-repeat ' . $name;
			}
			
		} elseif ($data['system'] === 'authorise' && set($form[$name])) {
			
			// здесь мы запишем первую запись для авторизации
			if (empty($login)) {
				$login = $form[$name];
			}
			
		} elseif ($data['system'] === 'language') {
			
			// здесь мы запишем текущий язык системы
			global $lang;
			$form[$name] = !empty($lang -> lang) ? $lang -> lang : null;
			
		} elseif ($data['system'] === 'datetime') {
			
			// здесь мы запишем текущее время в заданном формате
			$form[$name] = !empty($data['datetime']) ? datadatetime('', $data['datetime']) : time();
			
		} elseif ($data['system'] === 'allowip') {
			
			// здесь мы запишем текущий ip
			// при проверке он сверяется с массивом, так что все ок и ничего допиливать не нужно
			
			global $user;
			$form[$name][] = $user -> ip;
			
		} elseif ($data['system'] === 'allowagent') {
			
			// здесь мы запишем текущего агента
			// при проверке он сверяется с массивом, так что все ок и ничего допиливать не нужно
			
			$form[$name][] = md5(USER_AGENT);
			
		}
		
	}
	
	if (!empty($data['required']) && !set($form[$name])) {
		
		// если мы обнаружили поле, обязательное для заполнения
		// но оно отсутствует или пустое, записывается ошибка
		// важно сделать это условие отдельным, после всех
		// потому что поле может оказаться пустым после обработки
		
		$errors[] = 'required ' . $name;
		
	}
	
	if (!empty($data['crypt']) && set($form[$name])) {
		
		// если мы обнаружили поле, которое нужно закриптовать
		// это условие важно поставить после проверки на обязательность заполнения
		// потому что во время преобразования даже пустое поле закриптуется и окажется полным
		
		if ($data['crypt'] === 'password') {
			$form[$name] = password_hash($form[$name], PASSWORD_DEFAULT);
		} elseif ($data['crypt'] === 'hash') {
			$form[$name] = crypting($form[$name], 'hash');
		} else {
			$form[$name] = crypting($form[$name]);
		}
		
	}
	
	if (!empty($data['unique']) && set($form[$name])) {
		
		// если мы обнаружили поле, которое должно быть уникальным
		// нужно проверить его содержимое
		// важно сделать это условие самым последним, после всех обработок
		
		// здесь будет обращение к базе данных
		
		//echo 'mane: ' . $name . ':' . $form[$name];
		$try = dbUse('users', 'select', ['filter' => $name . ':' . $form[$name]]);
		
		//print_r($try);
		//echo '23';
		$try = set($try) ? true : false;
		
		if ($try) {
			$errors[] = 'unique ' . $name;
		}
		
		unset($try);
		
	}
	
}
unset($item, $name, $data);

// подбираем имя

// в одном случае в роли имени выступает значение первого поля, по которому будет идти авторизация
// после этого мы проверяем наличие этого имени в базе данных
// и если оно вдруг есть, проверяем, подтверждено ли оно и не истек ли срок подтверждения
// потому что если запись не подтверждена и у нее истек срок, ее нужно просто удалить

$try = false;
if (empty($errors)) {
	
	$name = $login;
	$try = dbUse('users:' . $name, 'select', ['return' => 'alone']);
	
	// добавлено удаление пользователя с истекшим сроком подтверждения
	if (empty($try)) {
		$try = true;
	} elseif (!empty($try['type'])) {
		$t = substr($try['type'], strrpos($try['type'], '.') + 1);
		if (!empty($t) && $t < time()) {
			dbUse('users:' . $name, 'delete');
			$try = true;
		} else {
			$try = false;
		}
		unset($t);
	} else {
		$try = false;
	}
	
}
unset($login);

// в другом случае это происходит таким образом
// мы генерируем имя как хэш от текущего времени и случайного числа и к хэшу тоже добавляем случайное число
// случайные числа нам нужны, чтобы предотвратить появление одинакового результата у разных пользователей
// после этого мы проверяем наличие такого имени в базе данных
// и если оно вдруг есть, генерируем имя повторно, для этого оборачиваем всю процедуру в цикл
// однако, чтобы цикл не ушел в бесконечность, мы добавляем ограничение на 100 попыток
/*
$try = false;
if (empty($errors)) {
	$attempt = 100;
	while(!$try) {
		
		$name = md5(time() . mt_rand()) . mt_rand(1000, 9999);
		$try = dbUse('users:' . $name, 'select', ['return' => 'alone']);
		
		// добавлено удаление пользователя с истекшим сроком подтверждения
		if (empty($try)) {
			$try = true;
		} elseif (!empty($try['type'])) {
			$t = substr($try['type'], strrpos($try['type'], '.') + 1);
			if (!empty($t) && $t < time()) {
				dbUse('users:' . $name, 'delete');
				$try = true;
			} else {
				$try = false;
			}
			unset($t);
		} else {
			$try = false;
		}
		
		if ($attempt <= 0) {
			break;
		} else {
			$attempt--;
		}
		
	}
	unset($attempt);
}
*/

// в случае неудачного исхода, мы прерываем процедуру
// хотя простого 'return' здесь недостаточно, т.к. процедура вызвана не функцией, а 'require'

if (!$try) {
	$errors[] = 'unique';
}

// проверяем на ошибки

if (!empty($errors)) {
	
	logging(htmlentities(print_r($errors, true)), 'registration was not completed');
	
} else {
	
	// читаем настройки регистрации пользователей
	
	$code = null;
	$link = null;
	$confirm = dataParse(USERS_REGISTER);
	
	if (objectIs($confirm)) {
		
		$ug = $confirm[0];
		
		if (empty($confirm[1])) {
			
			// если второго значения в настройках регистрации нет,
			// значит пользователь регистрируется в одну группу
			// и подтверждения на регистрацию не нужно
			
			$confirm = false;
			
		} else {
			
			// если есть второе значение в настройках регистрации,
			// значит пользователь будет переходить
			// из предварительной группы в конечную группу
			// и ему будет отправляться код подтверждения
			
			$confirm = true;
			
			// генерируем код подтверждения
			
			$code = ['', '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', 62, 32];
			for ($i = 0; $i < $code[3]; $i++) {
				$code[0] .= $code[1][rand(0, $code[2] - 1)];
			}
			$code = $code[0];
			//$code = $code[0] . '.' . (time() + TIME_DAY);
			
			// генерируем ссылку подтверждения
			
			$link = objectProcess('user:confirm');
			$link = $link['link'] . $link['string'] . '&close=1&data[user]=' . $name . '&data[code]=' . $code;
			
		}
		
	}
	
	// делаем запись в базу данных пользователей,
	// но только если USERS_REGISTER не пустой
	// однако эта запись остается пока неподтвержденной
	// поэтому делаем подтверждение записи, а код записываем в поле type
	
	$parameters = [
		'parent' => [$ug],
		'type' => !empty($confirm) ? $code : null,
		'data' => $form
	];
	
	$result = USERS_REGISTER ? dbUse('users:' . $name, 'write', $parameters) : null;
	
	// здесь нужно отсылать ссылку с кодом подтверждения пользователю
	// походу, функцию messageSend нужно делать системной и переименовывать, например в send
	// а message из template делать надстройкой
	
	if (!empty($confirm) && objectIs($send) && $result) {
		
		unset($result);
		
		foreach ($send as $item) {
			
			$result[] = send(
				(object) $item['data'],
				preg_replace(
					[
						'/\{confirm\}/ui', '/\{link\}/ui', '/\{site\}/ui', '/\{email\}/ui',
						'/\{confirm\:url\}/ui', '/\{link\:url\}/ui', '/\{site\:url\}/ui', '/\{email\:url\}/ui',
					],
					[
						$code, $link, !empty($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : $_SERVER['HTTP_HOST'], USERS_EMAIL,
						'<a href="' . $link . '">' . $code . '</a>', '<a href="' . $link . '">' . $link . '</a>', '<a href="' . !empty($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : $_SERVER['HTTP_HOST'] . '">' . !empty($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : $_SERVER['HTTP_HOST'] . '</a>', '<a href="mailto:' . USERS_EMAIL . '">' . USERS_EMAIL . '</a>'
					],
					$item['message']
				),
				$item['subject'],
				null
			);
			
		}
		
		unset($item, $code, $link, $confirm, $ug);
		
	} else {
		$result = null;
	}
	
}

unset($errors, $send);

if (set($result)) {
	echo 'ПОЛЬЗОВАТЕЛЬ УСПЕШНО СОЗДАН';
	if (objectIs($result)) {
		echo ' И ВАМ БЫЛО ОТПРАВЛЕНО ПИСЬМО ОБ ЕГО АКТИВАЦИИ';
	}
} elseif (USERS_REGISTER) {
	echo 'ЧТО-ТО ПОШЛО НЕ ТАК. ВОЗМОЖНО, ПОЛЬЗОВАТЕЛЬ УЖЕ СУЩЕСТВУЕТ';
} else {
	echo 'РЕГИСТРАЦИЯ ПОЛЬЗОВАТЕЛЕЙ ЗАПРЕЩЕНА';
}

/*
echo 'name:<br><pre>' . print_r($name, 1) . '</pre><hr>';
echo 'process:<br><pre>' . print_r($form, 1) . '</pre><hr>';
echo 'fields:<br><pre>' . print_r($fields, 1) . '</pre><hr>';
echo 'userstable:<br><pre>' . print_r($userstable, 1) . '</pre><hr>';
echo 'user:<br><pre>' . print_r($user, 1) . '</pre><hr>';
*/
?>