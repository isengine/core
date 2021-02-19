<?php defined('isPROCESS') or die;

init('class', 'content');
//require_once PATH_CORE . 'classes' . DS . 'content' . DS . 'content.php';

/*
// создаем новую переменную контента

$content = new Content([
	$process -> data['names'],
	$process -> data['parent'],
	$process -> data['type']
]);

$content -> settings();
$content -> read();

// сортируем данные

$content -> data = dbUse($content -> data, 'filter', [
	'sort' => $process -> data['sort'],
	'filter' => $process -> data['filter'],
	'skip' => $process -> data['skip'],
	'limit' => $process -> data['count']
]);

echo json_encode($content, JSON_UNESCAPED_UNICODE);
*/

$data = dataParse(base64_decode($process -> data['target']), false);

if (objectIs($data)) {
	foreach ($data as &$item) {
		$item = reset($item);
	}
	unset($item);
}

$content = new Content([
	$data['name'],
	$data['parent'],
	'alone'
]);

$content -> settings();
$content -> read();

$content -> ratingAdd($process -> data['name'], $process -> data['counter']);

/*
echo '<hr>DATA:<br>' . print_r($data, true);
echo '<hr>CONTENT:<br>' . print_r($content, true);
echo '<hr>PROCESS-DATA:<br>' . print_r($process -> data, true);
*/

//exit;

?>