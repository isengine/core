<?php defined('isENGINE') or die;

// https://habr.com/ru/post/358060/

// initial

init('init', 'fast');

$template = dbUse('templates:' . (!empty($_GET['template']) ? $_GET['template'] : 'default'), 'select', true);
if (!empty($template)) {
	$template = array_shift($template);
	$webapp = $template['webapp'];
} else {
	$webapp = null;
}
unset($template);

$uri -> site = $uri -> scheme . '://' . $uri -> host . '/';

$path = PATH_SITE . (!empty($webapp['custompath']) ? str_replace(':', DS, $webapp['custompath']) . DS : null) . $webapp['serviceworker'];
$sw = !empty($webapp['serviceworker']) && file_exists($path) ? localFile($path) : null;

header($_SERVER['SERVER_PROTOCOL'] . ' 200 OK', true, 200);
header('Content-type: application/javascript; charset=utf-8');
//header('Cache-Control: no-store, no-cache, must-revalidate');

echo $sw;

// read and echo content another file (as js) which set in template -> webapp
// and print push-messages

exit;

?>