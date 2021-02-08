<?php

$site = realpath($_SERVER['DOCUMENT_ROOT']) . DS;
$base = realpath($_SERVER['DOCUMENT_ROOT'] . DS . '..') . DS;
$core = realpath($_SERVER['DOCUMENT_ROOT'] . DS . '..') . DS;

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
		'core' => __DIR__,
		
		'assets' => $site . 'public' . DS . 'assets' . DS,
		'cache' => $site . 'cache' . DS,
		'content' => $site . 'public' . DS 'content' . DS,
		//'core' => $site . 'vendor' . DS . 'isengine' . DS . 'core' . DS,
		'custom' => $site . 'custom' . DS,
		'database' => $site . 'database' . DS,
		'errors' => 'error',
		'extensions' => $site . 'vendor' . DS,
		'storage' => $site . 'public' . DS . 'storage' . DS,
		'log' => $site . 'log' . DS,
		'process' => $site . 'vendor' . DS . 'isengine' . DS . 'core' . DS . 'process',
		'templates' => $site . 'public' . DS . 'templates' . DS
	],
	'name' => [
		'assets' => 'assets',
		'cache' => 'cache',
		'content' => 'content',
		'core' => 'core',
		'custom' => 'custom',
		'database' => 'database',
		'errors' => 'error',
		'extensions' => 'vendor',
		'storage' => 'storage',
		'log' => 'log',
		'process' => 'process',
		'templates' => 'templates'
	],
	'url' => [
		'assets' => '/public/assets/',
		'cache' => '/',
		'content' => '/',
		'core' => '/',
		'custom' => '/',
		'database' => '/',
		'extensions' => '/vendor/',
		'errors' => '/error/',
		'storage' => '/public/storage/',
		'log' => '/',
		'process' => '/process/',
		'templates' => '/public/templates/'
	]
];

unset($site, $base);

?>