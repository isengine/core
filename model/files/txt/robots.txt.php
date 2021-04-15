<?php defined('isENGINE') or die;

$robots = "# For more information about the robots.txt standard, see:
# http://www.robotstxt.org/orig.html
# For syntax checking, see:
# http://tool.motoricerca.info/robots-checker.phtml

User-agent: *\r\n";

if (defined('DEFAULT_NOINDEX') && constant('DEFAULT_NOINDEX')) {
	$robots .= "Disallow: /\r\n";
} else {
	
	$configuration = json_decode(file_get_contents(PATH_SITE . 'configuration.ini'), true);
	
	if (!empty($configuration['name'])) {
		$configuration = array_keys($configuration['name']);
	} else {
		$configuration = ['assets', 'cache', 'core', 'custom', 'libraries', 'log', 'modules', 'templates'];
	}
	
	$array = [];
	
	foreach ($configuration as $item) {
		$item = 'URL_' . strtoupper($item);
		$item = defined($item) ? substr(constant($item), 1) : null;
		if (
			!empty($item) &&
			$item !== '/' &&
			strpos($item, '//') === false &&
			!in_array($item, $array)
		) {
			$array[] = $item;
		}
	}
	unset($item, $configuration);
	
	if (defined('DEFAULT_PROCESSOR') && constant('DEFAULT_PROCESSOR')) {
		$array[] = str_replace(['/', '\\'], '/', constant('DEFAULT_PROCESSOR') . '/');
	}
	
	if (defined('DEFAULT_ERRORS') && constant('DEFAULT_ERRORS')) {
		$array[] = str_replace(['/', '\\'], '/', constant('DEFAULT_ERRORS') . '/');
	}
	
	if (defined('NAME_DATABASE') && constant('NAME_DATABASE')) {
		$configuration = substr(PATH_DATABASE, strlen(PATH_SITE));
		if (!empty($configuration)) {
			$array[] = str_replace(['/', '\\'], '/', $configuration);
		}
	}
	
	sort($array);
	
	foreach ($array as $key => &$item) {
		if (
			strpos($item, '/') + 1 < strlen($item) &&
			in_array(substr($item, 0, strpos($item, '/') + 1), $array)
		) {
			unset($array[$key]);
		} else {
			$robots .= "Disallow: /" . $item . "\r\n";
		}
	}
	unset($item, $array);
	
	//$robots .= "Disallow: /cgi-bin\r\nDisallow: /.*/\r\nDisallow: *?\r\n";
	$robots .= "Disallow: /cgi-bin\r\nDisallow: *?\r\n";
	
	if (
		defined('URL_LOCAL') &&
		constant('URL_LOCAL') &&
		constant('URL_LOCAL') !== '/' &&
		strpos(constant('URL_LOCAL'), '//') === false
	) {
		$robots .= "Allow: " . URL_LOCAL . "\r\n";
	}
	
	$robots .= "Allow: " . URL_ASSETS . "css/\r\n";
	$robots .= "Allow: " . URL_ASSETS . "fonts/\r\n";
	$robots .= "Allow: " . URL_ASSETS . "js/\r\n";
	$robots .= "Allow: " . URL_ASSETS . "less/\r\n";
	$robots .= "Allow: " . URL_ASSETS . "scss/\r\n";
	
}

$robots .= "Host: " . ($uri -> scheme === 'https' ? 'https://' : null) . $uri -> host . "\r\nCrawl-delay: 10" . (defined('DEFAULT_NOINDEX') && constant('DEFAULT_NOINDEX') ? null : "\r\nSitemap: " . $uri -> scheme . "://" . $uri -> host . "/sitemap.xml");

header($_SERVER['SERVER_PROTOCOL'] . ' 200 OK', true, 200);
header('Content-type: text/plain; charset=utf-8');
echo $robots;
unset($robots);
?>