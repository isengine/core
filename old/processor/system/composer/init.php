<?php defined('isPROCESS') or die;

$data = json_decode(base64_decode($process -> data['default']), true);

//echo print_r($data, true) . '<br><br>';

$names = null;
foreach ($data as $item) {
	$names .= ':' . $item['name'];
}

$a = dbUse('libraries' . $names, 'select', true);

foreach ($data as &$item) {
	if (!empty($a[$item['name']])) {
		$item['data'] = json_decode($item['data'], true);
		$item['data'] = array_merge($a[$item['name']], $item['data']);
		//$item['data'] = json_encode($item['data']);
	}
}

//print_r($a);
//echo '<hr>';
//print_r($data);

$b = dbUse('libraries', 'write', $data);

if (LOG_MODE === 'panic' || LOG_MODE === 'warning') {
	logging('composer process is ' . ($b ? 'complete' : 'error'));
}

echo 'Composer process is ' . ($b ? 'complete' : 'error') . '. You can reload this page!';

exit;

?>