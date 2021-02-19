<?php defined('isPROCESS') or die;

//error_reporting (E_ALL); 

require __DIR__ . DS . 'captcha.php';

$captcha = new isCaptcha();

/*
echo '[' . $captcha -> token . ']';
echo '<br><br>';
global $process;
print_r($process);
echo '<br><br>';
global $user;
print_r($user);
echo '<br><br>';
*/

global $user;
$name = str_replace(['.', ':'], '-', $user -> ip) . '-' . $captcha -> token;
$data = $captcha -> getKeyString();
$time = floor(time() / TIME_HOUR) * TIME_HOUR;

// удаляем старые записи капчей

$list = dbUse('captcha', 'select');

if (!empty($list)) {
	
	$del = [];
	
	foreach ($list as $item) {
		if ($item['type'] + TIME_HOUR / 2 < $time) {
			$del[] = $item['name'];
		}
	}
	unset($item);
	
	if (objectIs($del)) {
		dbUse('captcha:' . objectToString($del, ':'), 'delete');
	}
	
	unset($del);
	
}

// создаем новую запись капчи
// и если запись создана успешно, выводим изображение

if (dbUse('captcha:' . $name, 'write', ['type' => $time, 'data' => $data])) {
	$captcha -> getImage();
}

exit;

?>