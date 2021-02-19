<?php defined('isENGINE') or die;

if (
	empty($template -> place)
) {
	$template -> place = 'headopen';
} elseif ($template -> place === 'headopen') {
	$template -> place = 'headclose';
} elseif ($template -> place === 'headclose') {
	$template -> place = 'bodyopen';
} elseif ($template -> place === 'bodyopen') {
	$template -> place = 'bodyclose';
} else {
	$template -> place = null;
}

page('libraries', 'item', false);
page('scripts', 'item', false);

if (
	empty($template -> place) ||
	$template -> place === 'headopen'
) {
	echo "\r\n\r\n" . '<!--[if lt IE 9]>' . "\r\n";
	echo '<script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>' . "\r\n";
	echo '<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>' . "\r\n";
	echo '<![endif]-->' . "\r\n";
}

?>