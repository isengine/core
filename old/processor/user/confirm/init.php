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
Также он записывается куда-нибудь, например, в поле 'self', т.к. оно для пользователей не имеет смысла вообще,
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

кстати...

на момент 'process' уже загружены функции:
	ini
	data
	object

*/

$code = null;
$link = null;
$user = null;
$useris = null;
$result = null;

$data = $process -> data;
$confirm = dataParse(USERS_REGISTER);

//$user = dbUse('users:' . $data['user'], 'select', ['allow' => 'parent:' . $confirm[0] . ' type:' . $data['code']]);

if (objectIs($confirm)) {
	
	$user = dbUse('users:' . $data['user'], 'select');
	$useris = [
		dbUse($user, 'filter', ['allow' => 'parent:' . $confirm[0] . ' type:' . $data['code']]),
		dbUse($user, 'filter', ['deny' => 'parent:' . $confirm[0]])
	];
	
}

if (
	objectIs($useris[0]) &&
	count($useris[0]) == 1 &&
	!objectIs($useris[1])
) {
	
	$useris = array_shift($useris[0]);
	unset($useris['type'], $useris['parent']);
	$useris['parent'] = [$confirm[1]];
	
	$result = dbUse('users:' . $data['user'], 'delete');
	$result = $result ? dbUse('users', 'write', [$useris]) : null;
	
}

if ($result) {
	echo 'ВСЕ В ПОРЯДКЕ';
} else {
	echo 'ЧТО-ТО ПОШЛО НЕ ТАК. ВОЗМОЖНО, ПОЛЬЗОВАТЕЛЬ УЖЕ ПОДТВЕРЖДЕН';
}

echo '<br><a href="' . objectGet('uri', 'previous') . '">вернуться</a><br><a href="/">перейти на главную</a>';

//print_r($data);
//print_r($confirm);

/*
$usertest = dbUse('users:' . $data['user'], 'select');

echo 'USER<pre>' . print_r($user, 1) . '</pre><br>';
echo 'IS<pre>' . print_r($useris, 1) . '</pre><br>';
echo 'TEST<pre>' . print_r($usertest, 1) . '</pre><br>';
*/

?>