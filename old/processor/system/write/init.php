<?php defined('isPROCESS') or die;

// что плохо?
// что имя и база данных передаются в запросе,
// соответственно, если их подделать,
// то можно заменить любую запись в любом разделе
// если, конечно, есть на это права и доступ

// какие есть варианты?
// генерировать сервером токен через pasword и отправлять в какое-то поле
// а затем сравнивать это поле поочередно с именами разделов базы данных
// то, которое совпадет, и будет верным
// НО! зная эту технология, имя все равно можно подделать

$db = $_SESSION['writedb'];
$dbmerge = $process -> data['db'];
$name = $process -> data['name'];

$data = iniPrepareJson($process -> data['data'], true);
//$data = $process -> data['data'];

//unset($db);

//$result = dbUse($db . ':' . $name, 'select');

//print_r($process);
//print_r($result);

/*
dbUse('users:' . $name, 'delete');

$parameters = [
	'parent' => [$ug],
	'type' => !empty($confirm) ? $code : null,
	'data' => $form
];
dbUse('users:' . $name, 'write', $parameters);
*/

if (
	!empty($db) &&
	!empty($dbmerge) &&
	!empty($name) &&
	objectIs($data) &&
	$db === $dbmerge
) {
	
	//echo print_r($process -> data['data'], true);
	
	if (!empty($process -> data['origin'])) {
		$origin = iniPrepareJson($process -> data['origin'], true);
		$data = array_replace_recursive($origin, $data);
	}
	
	$parameters = [
		'data' => $data
	];
	
	if (!empty($process -> data['parent'])) {
		$parameters['parent'] = iniPrepareJson($process -> data['parent'], true);
	}
	
	if (!empty($process -> data['type'])) {
		$parameters['type'] = $process -> data['type'];
	}
		
	$return = dbUse($db . ':' . $name, 'write', $parameters);
	if ($return) {
		echo iniPrepareArray($data);
	}
	
}

exit;

?>