<?php

namespace is;

use is\Helpers\Local;

$path = file_get_contents(DR . 'config' . DS . 'path.ini');
$path = json_decode($path, true);

foreach ($path as $key => $item) {
    $item = ($key === 'assets' ? DI : DR) . preg_replace('/[\:\/\\\\]+/ui', DS, $item);
    if (!file_exists($item)) {
        Local::createFolder($item);
    }
    $default[$key] = realpath($item) . DS;
}
unset($key, $item, $path);
