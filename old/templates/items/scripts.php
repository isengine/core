<?php defined('isENGINE') or die;

global $loadingLog;
global $uri;

$place = empty($template -> place) ? 'headopen' : $template -> place;
$print = "\r\n\r\n" . '<!-- SCRIPTS ' . ($place === 'headopen' ? '' : 'IN ' . strtoupper($place) . ' PLACE ') . '-->' . "\r\n\r\n";

$link = [
	'css' => ['<link rel="stylesheet" rev="stylesheet" type="text/css" href="', '" />'],
	'js' => ['<script ' . (in('options', 'oldbrowsers') ? 'type="text/javascript" ' : null) . 'src="', '"></script>']
];

if (objectIs($template -> settings -> assets[$place]['js'])) {
	foreach ($template -> settings -> assets[$place]['js'] as $item) {
		
		$prefix = localLink(
			'js/' . $item . '.js',
			'assets',
			in('options', 'updatelocal') && $template -> modified ? $template -> modified : true,
			'min'
		);
		
		if (!empty($prefix)) {
			$print .= $link['js'][0] . $prefix . $link['js'][1];
		}
		
		unset($prefix);
		
	}
	unset($item, $str);
}

echo $print;
unset($print, $prefix);

//print_r($template -> settings -> assets);

?>