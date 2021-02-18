<?php

$site = realpath($_SERVER['DOCUMENT_ROOT']) . DS;
$base = DR;
$core = realpath(__DIR__ . DS . DP) . DS;

$default = [
	'system' => [
		'php' => '7.0',
		'session' => 'SID',
		'charset' => 'UTF-8',
		'local' => '127.0.0.1',
		'sender' => 'X-Mailer: PHP/' . phpversion()
	],
	'time' => [
		'minute' => 60,
		'hour' => 3600,
		'day' => 86400,
		'week' => 604800,
		'month' => 2628000,
		'year' => 31556926
	],
	'path' => [
		'site' => $site,
		'base' => $base,
		'core' => $core,
		
		'app' => $base . 'app' . DS,
		'assets' => $site . 'assets' . DS,
		'cache' => $site . 'cache' . DS,
		'database' => $base . 'database' . DS,
		'extensions' => $base . 'vendor' . DS,
		'log' => $base . 'log' . DS,
		'templates' => $base . 'templates' . DS
	],
	'name' => [
		'app' => 'app',
		'assets' => 'assets',
		'cache' => 'cache',
		'database' => 'database',
		'extensions' => 'vendor',
		'log' => 'log',
		'templates' => 'templates'
	],
	'url' => [
		'app' => '/',
		'assets' => '/assets/',
		'cache' => '/cache/',
		'database' => '/',
		'extensions' => '/',
		'log' => '/',
		'templates' => '/'
	],
	'error' => [
		'name' => 'error',
		'url' => '/error/',
		'prefix' => '',
		'postfix' => ''
	],
	'filter' => [
		'name' => 'filter',
		'url' => '/filter/'
	],
	'api' => [
		'name' => 'api',
		'url' => '/api/'
	],
	'router' => [
		'folders' => [
			'convert' => null,
			'index' => null,
			'extension' => null
		],
		'files' => [
			'convert' => null,
			'index' => null,
			'extension' => null
		],
		'convert' => [
			'from' => null,
			'to' => null
		],
		'index' => null,
		'reload' => null
	]
];

unset($site, $base, $core);

?>