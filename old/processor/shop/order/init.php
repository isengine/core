<?php defined('isENGINE') or die;

// для запуска/вызова процессов через модуль вывода форм нужно сделать так:
// указывать исходную форму для проверки данных и затем данные песочить по этой форме

// для заказа используется таблица order в базе данных
// где имя - дата и время заказа (год-мес-день-часы-мин-сек-мсек), плюс сгенерированное случайное число
// родитель - магазин
// self - пользователь, которому заказ принадлежит (пустое поле, если пользователя нет)
// в данных заказа:
// состав заказа по артикулам и числу
// рассчитанные суммы

if (
	!defined('CORE_CONTENT') ||
	!defined('CORE_SHOP') ||
	!CORE_CONTENT ||
	!CORE_SHOP
) {
	exit;
}

// загружаем функции

init('functions', 'local');
init('functions', 'templates');
init('functions', 'math');

// инициализируем классы

init('class', 'content');
init('class', 'shop');

// устанавливаем цель

$target = 'default';

if (!empty($process -> source['this'])) {
	$target = iniPrepareJson(base64_decode($process -> source['this']), true);
}

if (!objectIs($target)) {
	$target = dataParse($target);
}

if (empty($target[1])) {
	$target[1] = $target[0];
}

global $uri;

if (
	!empty($uri -> query -> array['status']) &&
	$uri -> query -> array['status'] === 'complete'
) {
	
	$complete = true;
	$shop = null;
	$cart = null;
	$prices = null;
	$sales = null;
	
} else {
	
	$complete = null;

	$shop = new Shop($target[0]);
	$shop -> read($target[1]);
	
	$cart = &$shop -> cart;
	$prices = &$shop -> prices;
	$sales = &$shop -> sales;
	
}

global $user;
$microtime = microtime();
$name = substr($microtime, strpos($microtime, ' ') + 1) . '_' . substr($microtime, 2, 3) . '_' . mt_rand(1000, 9999);

$data = [
	'name' => $name,
	'type' => null,
	'parent' => [$target[0], $target[1]],
	'self' => [$user -> name],
	'data' => [
		// * - временно пока не научим распознавать данные в модуле таблице или заказа
		'user' => $user -> name,
		'uname' => $user -> data['name'], // *временно
		'uphone' => $user -> data['phone'], // *временно
		'uemail' => $user -> data['email'], // *временно
		'time' => time(),
		'shop' => $target[0],
		'content' => $target[1],
		'cart' => $cart,
		'prices' => $prices,
		'ptotal' => $prices['total'], // *временно
		'sales' => $sales,
		'order' => $shop -> order,
		'data' => $process -> data,
		'daddress' => $process -> data['address'], // *временно
		'dtext' => $process -> data['primechaniya'] // *временно
	]
];

//echo '<pre>' . print_r($data, 1) . '</pre>';
//echo '<pre>' . print_r($user, 1) . '</pre>';
//echo '<pre>' . print_r($shop, 1) . '</pre>';
//echo '<pre>' . print_r($process, 1) . '</pre>';
//exit;

$complete = dbUse('orders', 'write', [$data]);

if ($complete) {
	cookie('cart');
	if (objectIs($shop -> settings['send'])) {
		send(
			$shop -> settings['send'],
			$shop -> settings['send']['message'],
			$shop -> settings['send']['subject'],
			$data['data']
		);
	}
}

header("Location: /" . $uri -> previous . (!empty($complete) ? '?complete' : null));
exit;

?>