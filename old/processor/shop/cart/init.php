<?php defined('isPROCESS') or die;

global $uri;

$data = $process -> data;

// процесс с несколькими типами:
// default или тип не задан - добавить товар в корзину
// причем, если не указано количество, то считается, что добавляется единица товара
// если товара нет в коризне, то он добавляется с заданным количеством
// если товар есть в корзине, то прибавляется заданное количество
// если указано отрицательное число, то считается, что товар убавляется на заданное количество
// с отрицательным числом, если товара нет в корзине, то ничего не происходит
// с отрицательным числом, если товар есть в корзине, то убавляется заданное количество
// с отрицательным числом, если итоговое количество товара 0 и меньше, то товар удаляется из корзины
// change - изменить количество товара в корзине
// если не указано количество, то считается, что его 0
// количество товара в корзине изменяется на заданное число
// если итоговое количество товара 0 и меньше, то товар удаляется из корзины

$cart = cookie('cart', true);

if (!empty($cart)) {
	$cart = iniPrepareJson($cart, true);
} else {
	$cart = [];
}

$change = !empty($process -> source['change']) ? true : false;

foreach ($data as $key => $item) {
	
	if (objectIs($item)) {
		
		ksort($item, SORT_NATURAL | SORT_FLAG_CASE);
		
		$num = array_shift($item);
		if (objectIs($item)) {
			$key .= ':' . objectToString($item, ':');
		}
		
		$item = $num;
		unset($num);
		
	}
	
	$item = (float) $item;
	
	if (objectIs($cart) && array_key_exists($key, $cart)) {
		
		if ($change) {
			$cart[$key] = $item;
		} elseif ($cart[$key] <= 0) {
			// иногда происходит сбой и количество заказа становится отрицательным
			// чтобы не путать людей, в таких случаях мы просто обновляем указанную позицию
			$cart[$key] = empty($item) ? 1 : $item;
		} else {
			$cart[$key] += empty($item) ? 1 : $item;
		}
		
		if (empty($cart[$key]) || $cart[$key] < 0) {
			unset($cart[$key]);
		}
		
	} elseif (!$change || !empty($item)) {
		
		// здесь все правильно - это точно (!)
		
		$cart[$key] = empty($item) ? 1 : $item;
		
	}
	
}
unset($key, $item);

$cart = objectIs($cart) ? iniPrepareArray($cart) : null;

cookie('cart', $cart);

//echo print_r($data, true) . '<br><br>';
//echo print_r($c0, true) . '<br><br>';
//echo print_r($_COOKIE, true) . '<br><br>';

reload('/' . $uri -> previous);
//header('Location: /' . $uri -> previous);
//exit;

?>