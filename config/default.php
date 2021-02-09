<?php

$site = realpath($_SERVER['DOCUMENT_ROOT']) . DS;
$base = DR;
$core = realpath(__DIR__ . DS . DP) . DS;

$default = [
	'system' => [
		'php' => '5.6.0',
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
		
		'assets' => $base . 'public' . DS . 'assets' . DS,
		'cache' => $base . 'public' . DS . 'cache' . DS,
		'custom' => $base . 'custom' . DS,
		'database' => $base . 'database' . DS,
		'extensions' => $base . 'vendor' . DS,
		'log' => $base . 'log' . DS,
		'templates' => $base . 'templates' . DS
	],
	'name' => [
		'default' => 'default',
		'errors' => 'error',
		'items' => 'items',
		'process' => 'process',
		
		'assets' => 'assets',
		'cache' => 'cache',
		'custom' => 'custom',
		'database' => 'database',
		'extensions' => 'vendor',
		'log' => 'log',
		'templates' => 'templates'
	],
	'url' => [
		'assets' => '/assets/',
		'cache' => '/cache/',
		'custom' => '/',
		'database' => '/',
		'extensions' => '/',
		'log' => '/',
		'templates' => '/'
	]
];

unset($site, $base, $core);

?>