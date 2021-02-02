<?php

$site = realpath($_SERVER['DOCUMENT_ROOT']) . DS;
$base = realpath($_SERVER['DOCUMENT_ROOT'] . DS . '..') . DS;

$default = [
	'system' => [
		'php' => '5.6.0',
		'session' => 'SID',
		'charset' => 'UTF-8',
		'local' => '127.0.0.1',
		'sender' => 'X-Mailer: PHP/' . phpversion()
	],
	'path' => [
		'site' => $site,
		'base' => $base,
		'assets' => $site . 'public' . DS . 'assets' . DS,
		'cache' => $site . 'cache' . DS,
		'core' => $site . 'vendor' . DS . 'isengine' . DS . 'core' . DS,
		'custom' => $site . 'public' . DS . 'custom' . DS,
		'database' => $site . 'database' . DS,
		'extensions' => $site . 'vendor' . DS,
		'local' => $site . 'public' . DS . 'local' . DS,
		'log' => $site . 'log' . DS,
		'templates' => $site . 'public' . DS . 'templates' . DS
	],
	'name' => [
		'assets' => 'assets',
		'cache' => 'cache',
		'core' => 'core',
		'custom' => 'custom',
		'database' => 'database',
		'extensions' => 'vendor',
		'local' => 'local',
		'log' => 'log',
		'templates' => 'templates'
	],
	'url' => [
		'assets' => '/public/assets/',
		'cache' => '/cache/',
		'core' => '/',
		'custom' => '/',
		'database' => '/',
		'extensions' => '/vendor/',
		'local' => '/public/local/',
		'log' => '/log/',
		'templates' => '/public/templates/'
	]
];

unset($site, $base);

?>