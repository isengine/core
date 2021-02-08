<?php

// Проверяем версию php

if (version_compare(PHP_VERSION, CMS_MINIMUM_PHP, '<')) {
	error('php', false, true);
}

// Проверяем существование модулей php

$phpmods = [
	'mbstring'
];

foreach ($phpmods as $item) {
	if (!extension_loaded($item)) {
		error('system', false, '[not php module : ' . $item . ']');
	}
}

// Проверяем взаимодействие констант

if (
	!DEFAULT_USERS && (SECURE_RIGHTS || SECURE_CSRF || USERS_RIGHTS || SECURE_USERS) ||
	SECURE_WRITING && (!defined(DB_WRITINGUSER) || !defined(DB_WRITINGPASS) || !DB_WRITINGUSER || !DB_WRITINGPASS)
) {
	error('system', false, '[system constants is set wrong : ' . 
		'SECURE_RIGHTS or SECURE_CSRF or USERS_RIGHTS or SECURE_USERS without DEFAULT_USERS // ' .
		'SECURE_RIGHTS without USERS_RIGHTS // ' .
		'SECURE_WRITING without DB_WRITINGUSER or DB_WRITINGPASS' .
	']');
}

// Проверяем существование системных папок

$folders = [
	'assets', 'database', 'cache', 'core', 'libraries', 'local', 'log', 'modules', 'templates'
];

foreach ($folders as $item) {
	$item = strtoupper($item);
	if (
		!defined('NAME_' . $item) ||
		!defined('URL_' . $item) ||
		!defined('PATH_' . $item) ||
		!file_exists(constant('PATH_' . $item)) ||
		!is_dir(constant('PATH_' . $item))
	) {
		if (
			!file_exists(constant('PATH_' . $item)) &&
			SECURE_BLOCKIP === 'developlist'
		) {
			mkdir(constant('PATH_' . $item));
		} else {
			error('system', false, '[not system folder from path constant : ' . $item . ']');
		}
	}
}

unset($item, $phpmods, $constants, $folders, $functions);

?>