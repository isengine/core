<?php defined('isENGINE') or die;

global $uri;
global $structure;
global $template;

$template = (object) [
	'name' => reset($uri -> path -> array),
	'created' => null,
	'modified' => null,
	'place' => null,
	'section' => null,
	'page' => [],
	'path' => (object) [
		//'array' => [],
		'init' => PATH_TEMPLATES,
		//'url' => URL_TEMPLATES,
		// нужно ли здесь определять языки? тогда нужно $uri -> site . $uri -> path -> base . URL_TEMPLATES
		// это $template -> path -> url
		// похоже, нет, потому что этот урл будет использоваться для загрузки локальных файлов шаблона
		// значит, язык здесь не важен и даже не нужен
		'page' => null
	],
	'settings' => (object) [
		'libraries' => null,
		'options' => null,
		'assets' => null,
		'special' => null
	],
	'device' => null,
	'script' => null,
	'list' => (object) [
		'folders' => localList(PATH_TEMPLATES, ['return' => 'folders', 'skip' => ['default', DEFAULT_ERRORS]]),
		'router' => !empty($uri -> path -> array) ? $uri -> path -> array : null, // было так: ['home']
		'structure' => structureUngroup($structure)
	]
];

// подготавливаем список доступных шаблонов

foreach ($template -> list -> folders as &$item) {
	$item = substr($item, 0, -1);
}
unset($item);

?>