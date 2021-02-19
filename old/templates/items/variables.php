<?php defined('isENGINE') or die;

if (
	empty($template) ||
	empty($template -> settings) ||
	!objectIs($template -> settings -> variables)
) {
	return false;
}

$varsjs = null;

foreach ($template -> settings -> variables as $key => $item) {
	
	$key = dataParse($key);
	
	if (empty($key[1]) || $key[1] === 'php') {
		$template -> var[$key[0]] = $item;
	}
	
	if (empty($key[1]) || $key[1] === 'js') {
		if (empty($key[2])) {
			$varsjs .= $key[0] . '="' . $item . '", ';
		} else {
			$varsjs .= $key[0] . '=' . $item . ', ';
		}
	}
	
}

unset($key, $item);

if (!empty($varsjs)) {
	echo "<!-- SCRIPT VARIABLES -->\r\n<script type=\"text/javascript\">\r\n\tvar " . substr($varsjs, 0, -2) . ";\r\n</script>";
}

unset($varsjs);

?>