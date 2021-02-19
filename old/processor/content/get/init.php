<?php defined('isPROCESS') or die;

/*

$process -> data = [
	'names' => ...
	'parent' => ...
	'type' => ...
	'count' => ...
	'skip' => ...
	'sort' => ...
	'filter' => ...
];

$process -> data['names']
$process -> data['parent']
$process -> data['type']

$process -> data['count']
$process -> data['skip']

$process -> data['sort']
$process -> data['filter']

"names" : "",
"parent" : null,
"type" : "asc",
"count" : ""
"skip" : ""
"sort" : ""
"filter" : "" // filterstring

*/

// загружаем необходимые файлы

//init('functions', 'local');
//init('functions', 'templates');
//init('functions', 'math');

init('class', 'content');
//require_once PATH_CORE . 'classes' . DS . 'content' . DS . 'content.php';

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

exit;

?>