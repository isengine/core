<?php defined('isENGINE') or die;

global $template;

$autoload = PATH_LIBRARIES;
$options = in('options');

if (!empty($options)) {
	foreach ($options as $i) {
		if (stripos($i, 'autoload') !== false) {
			$i = substr($i, 9);
			if (!empty($i)) {
				$autoload = PATH_SITE . str_replace(':', DS, $i) . DS;
			}
			break;
		}
	}
}

$autoload .= 'autoload.php';
$autoload = str_replace(DS . DS, DS, $autoload);

if (!file_exists($autoload)) {
	error('403', false, 'not exists autoload file for composer in \'' . $template -> name . '\' template');
} else {
	require_once $autoload;
}

unset($autoload, $options);

?>