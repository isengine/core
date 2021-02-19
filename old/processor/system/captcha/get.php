<?php defined('isPROCESS') or die;

# isCaptcha change base configuration

global $process;

if (objectIs($process -> data)) {
	$allow = ['token', 'font', 'symbols', 'length', 'width', 'height', 'amplitude', 'waves', 'rotate', 'blacknoise', 'whitenoise', 'linenoise', 'lines', 'no_spaces', 'color', 'bgcolor'];
	foreach ($process -> data as $key => &$item) {
		if (
			!empty($item) &&
			(is_string($item) || is_numeric($item)) &&
			in_array($key, $allow)
		) {
			if ($item === 'disable') {
				$item = false;
			} else {
				$item = rawurldecode($item);
				$item = html_entity_decode($item);
			}
			$$key = $item;
		} else {
			unset($process -> data[$key]);
		}
	}
}

# isCaptcha image colors convert to RGB, 0-255

function captchaColors($i) {
	if ($i[0] === '#') {
		$i = substr($i, 1);
	} if (strlen($i) !== 6) {
		$i = $i[0] . $i[0] . $i[1] . $i[1] . $i[2] . $i[2];
	}
	$i = array(
		hexdec($i[0] . $i[1]),
		hexdec($i[2] . $i[3]),
		hexdec($i[4] . $i[5])
	);
	return $i;
}

$foreground_color = captchaColors($color);
$background_color = captchaColors($bgcolor);

$select_font = $font;
unset($font);

if ($symbols === 'alphanumeric') {
	$symbols = '346789abcdegkpqvxy';
} elseif ($symbols === 'numeric') {
	$symbols = '0123456789';
} else {
	$symbols = 'abcdeghiklmpqrstuxyz';
}

?>