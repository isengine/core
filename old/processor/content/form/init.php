<?php defined('isPROCESS') or die;

init('class', 'content');
//require_once PATH_CORE . 'classes' . DS . 'content' . DS . 'content.php';

$error = null;

$parameters = [
	'name' => null,
	'parent' => dataParse($process -> set -> status),
	'self' => null,
	'data' => []
];

$content = new Content([null, reset($parameters['parent'])]);
$content -> settings();

$sets = $content -> settings['create'];

foreach ($sets as $key => $item) {
	
	if (array_key_exists($key, $process -> data)) {
		
		$item = dataParse($item);
		if (!objectIs($item)) { $item = [null, null]; }
		
		//echo $key . ':' . print_r($item, true) . '<br>';
		
		$parameters['data'][$key] = clear($process -> data[$key], $item[0]);
		
		if (!empty($item[2]) && !set($parameters['data'][$key])) {
			$error[] = $key;
		}
		
	}
	
}

unset($key, $item, $sets);

if (!empty($parameters['data']['name'])) {
	$parameters['name'] = $parameters['data']['name'];
} else {
	$parameters['id'] = $parameters['name'] = time() . str_pad(mt_rand(0, 9999), 4, '0', STR_PAD_LEFT);
}

global $user;

if (!empty($user -> name)) {
	$parameters['self'] = $user -> name;
}

/*
echo '<hr>USER:<br>' . print_r($user, true);
echo '<hr>SETS:<br>' . print_r($sets, true);
echo '<hr>DATA:<br>' . print_r($parameters, true);
echo '<hr>CONTENT:<br>' . print_r($content, true);
echo '<hr>PROCESS:<br>' . print_r($process, true);
echo '<hr>PROCESS-DATA:<br>' . print_r($process -> data, true);
*/

if (empty($error)) {
	dbUse('content', 'write', [$parameters]);
} else {
	logging('content in \'' . objectToString($parameters['parent'], ':') . '\' was not writing because required data fields \'' . objectToString($error, ':') . '\' was not set or was clearing', 'content was not writing');
}

?>