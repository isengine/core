<?php defined('isENGINE') or die;

global $uri;

$print = "\r\n\r\n" . '<!-- FAVICONS -->' . "\r\n\r\n";

$icons = dbUse('icons', 'select', true);
$path = $uri -> site . mb_substr(URL_LOCAL, 1) . $icons['settings']['path'] . '/';

if (!empty($icons['favicon'])) {
	$print .= '<link rel="icon" type="image/x-icon" href="' . (empty($icons['favicon']['rootfolder']) ? $path : $uri -> site) . (empty($icons['favicon']['name']) ? 'favicon' : $icons['favicon']['name']) . '.ico">';
	$print .= '<link rel="shortcut icon" type="image/vnd.microsoft.icon" href="' . (empty($icons['favicon']['rootfolder']) ? $path : $uri -> site) . (empty($icons['favicon']['name']) ? 'favicon' : $icons['favicon']['name']) . '.ico">';
}

if (!empty($icons['safari'])) {
	// здесь должен быть svg, но svg пока не генерируем, а генератор подразумевается как png, закодированный в base64 и обернутый в svg
	$print .= '<link rel="mask-icon" href="' . $path . $icons['safari']['name'] . '.png"' . (!empty($icons['safari']['color']) ? ' color="' . $icons['safari']['color'] . '"' : '') . '>';
}

unset($icons['settings'], $icons['splashscreen'], $icons['webapp'], $icons['favicon'], $icons['safari'], $icons['msapplication']);

if ($icons) {
	foreach ($icons as $key => $item) {
		foreach ($item['sizes'] as $i) {
			$print .= '<link rel="' . $key . '" ' . ($key === 'icon' ? 'type="image/png" ' : '') . 'sizes="' . $i . 'x' . $i . '" href="' . $path . $item['name'] . '-' . $i . 'x' . $i . '.png">';
		}
	}
	unset($item, $i, $key);
}

unset($icons);

echo $print;
unset($print);

?>