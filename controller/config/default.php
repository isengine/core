<?php

// Рабочее пространство имен

namespace is;

use is\Helpers\System;

$site = System::server('root');
$base = DR;
$core = realpath(__DIR__ . DS . DP) . DS;

$default = [
	'system' => [
		'php' => '5.6',
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
		'cache' => $base . 'cache' . DS,
		'extensions' => $base . 'vendor' . DS,
		'log' => $base . 'log' . DS,
		'templates' => $base . 'templates' . DS
	],
	'api' => [
		'server' => null,
		'name' => 'api',
		'key' => 'key',
		'token' => 'token'
	],
	'error' => [
		'url' => '/error/',
		'prefix' => '',
		'postfix' => '',
		'template' => 'error'
		
		//'url' => '/',
		//'prefix' => 'e',
		//'postfix' => '.html'
		
		//'url' => '/',
		//'prefix' => '?error=',
		//'postfix' => ''
	],
	'url' => [
		'assets' => '/assets/',
		'data' => [
			'rest' => 'data',
			'query' => true
		]
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
		'reload' => true
	]
];

unset($site, $base, $core);

?>