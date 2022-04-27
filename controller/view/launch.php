<?php

namespace is;

use is\Helpers\System;
use is\Helpers\Strings;
use is\Helpers\Objects;
use is\Components\Config;
use is\Components\State;
use is\Components\Display;
use is\Components\Log;
use is\Components\Router;
use is\Masters\Generator;

// читаем

$state = State::getInstance();
$config = Config::getInstance();
$router = Router::getInstance();
$file = Generator::getInstance();

// file generator

if ($file->exists) {
    $file->launch();
}

// section

$section = $state->get('section') && $router->template['section'] ? 'sections:' . $router->template['section'] . ':' : null;

// cache header

if (!$state->get('error') && !$section) {
    $path = __DIR__;
    System::includes('cache', $path);
}

// include template

System::includes(
    'html:' . $section . 'template',
    $config->get('path:templates') . $router->template['name']
);

//echo '<pre>';
//echo print_r($path, 1);
//echo '</pre>';
