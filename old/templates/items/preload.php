<?php defined('isENGINE') or die;

global $libraries;
global $uri;

//echo '<!-- ' . print_r($libraries, 1) . ' -->';
//echo '<!-- ' . print_r($template -> settings -> assets, 1) . ' -->';

$link = [
	'preload' => ['<link rel="preload" href="', '"', ' />', ' as="', '"', ' crossorigin'],
];

$print = "\r\n\r\n<!-- PRELOAD -->\r\n\r\n";

foreach ($libraries as $item) {
	
	$cdn = !empty($item['variant']) && strpos($item['variant'], 'cdn') !== false ? true : false;
	
	if (objectIs($item['data']['preload'])) {
		foreach ($item['data']['preload'] as $i) {
			$i = dataParse($i);
			$print .= $link['preload'][0] . (!$cdn ? URL_LIBRARIES : null) . $i[0] . $link['preload'][1] . (!empty($i[1]) ? $link['preload'][3] . $i[1] . $link['preload'][4] . ($i[1] === 'font' ? $link['preload'][5] : null) : null) . $link['preload'][2];
		}
		unset($i);
	}
	
}
unset($item, $cdn);

foreach ($template -> settings -> assets as $item) {
	if (objectIs($item['preload'])) {
		foreach ($item['preload'] as $i) {
			$i = dataParse($i);
			$print .= $link['preload'][0] . URL_ASSETS . $i[0] . $link['preload'][1] . (!empty($i[1]) ? $link['preload'][3] . $i[1] . $link['preload'][4] . ($i[1] === 'font' ? $link['preload'][5] : null) : null) . $link['preload'][2];
		}
		unset($i);
	}
}
unset($item);

echo $print;
unset($print, $link);

?>