<?php

// Рабочее пространство имен

namespace is;

use is\Model\Constants\Config;
use is\Model\Data\LocalData;
use is\Parents\Path;
use is\Parents\Local;

$p = __DIR__ . DS . DP;
echo $p . '<br>';

$a = new Path($p);
$a -> include('composer');
//echo print_r($a -> real, 1) . '<br>';

$local = new Local();
$data = new LocalData($local);
$local -> setFile('configuration.ini');
$data -> joinData($local);
$local -> setFile('configuration.local.ini');
$data -> joinData($local);

//echo print_r($local, 1) . '<br>';
echo '<pre>' . print_r($data, 1) . '</pre><br>';


$config = Config::getInstance();

$config -> data = [
	/*
	"system" => [
	],
	"path" => [
	],
	"default" => [
		"timezone" => "",
		"scheme" => "",
		"host" => "",
		"processor" => "process",
		"errors" => "error",
		"lang" => "ru",
		"users" => true,
		"page" => true,
		"custom" => false,
		"noindex" => true,
		"mode" => "develop"
	],
	"core" => [
		"composer" => true,
		"content" => true,
		"html" => true,
		"shop" => true
	],
	"secure" => [
		"request" => true,
		"blockip" => "blacklist",
		"users" => true,
		"rights" => true,
		"sessiontime" => "60",
		"processtime" => "600",
		"csrf" => false,
		"writing" => false
	],
	"log" => [
		"mode" => "warning",
		"sort" => "name",
		"data" => true
	]
	*/
];

$get = $config->get();

echo '<pre>' . print_r($config, 1) . '</pre>';
echo '<pre>' . print_r($get, 1) . '</pre>';

echo '12312';

//if (!defined('PATH_SITE')) { define('PATH_SITE', realpath($_SERVER['DOCUMENT_ROOT']) . DS); }
//if (!defined('PATH_BASE')) { define('PATH_BASE', realpath($_SERVER['DOCUMENT_ROOT'] . DS . '..') . DS); }

?>