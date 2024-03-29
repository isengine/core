<?php

namespace is;

$path = realpath(__DIR__ . DS . DP . DP) . DS;

$autoload = $path . 'autoload.php';

if (file_exists($autoload)) {
    require_once $autoload;
} else {
    // framework

    $autoload = $path . 'isengine' . DS . 'isphp' . DS . 'init.php';
    require_once $autoload;

    // classes

    $list = file_get_contents(DR . 'config' . DS . 'classes.ini');
    $list = json_decode($list, true);

    if (!empty($list)) {
        foreach ($list as $item) {
            $item = $path . str_replace(['\\', '/', ':'], DS, $item) . '.php';
            require_once $item;
        }
        unset($item);
    }

    unset($list, $path);

    // core model, app and modules

    $path = file_get_contents(DR . 'config' . DS . 'path.ini');
    $path = json_decode($path, true);

    $paths = [
        'app' => $path['app'] ? DR . preg_replace('/[\:\/\\\\]+/ui', DS, $path['app']) . DS : null,
        'core' => realpath(__DIR__ . DS . 'model') . DS,
        'vendors' => $path['vendors'] ? DR . preg_replace('/[\:\/\\\\]+/ui', DS, $path['vendors']) . DS : null
    ];

    spl_autoload_register(function ($class) use ($paths) {
        if (class_exists($class)) {
            return;
        }

        $array = explode('\\', $class);
        array_shift($array);

        $module = mb_strpos($class, 'is\\Masters\\Modules\\', 0);
        $module = $module || $module === 0 ? true : null;

        $path = implode(DS, $array);
        $vendors = $module ? mb_strtolower(implode(DS, array_slice($array, 2, 2))) : null;

        foreach ($paths as $key => $item) {
            if ($module) {
                $patha = implode(DS, array_slice($array, 0, 4));
                $pathb = implode(DS, array_slice($array, 3));
                $file = $item . (
                    $key === 'vendors' && $vendors ? $vendors : $patha
                ) . DS . 'class' . DS . $pathb . '.php';
                unset($patha, $pathb);
                if (file_exists($file)) {
                    require_once $file;
                }
            } elseif ($key !== 'vendors') {
                $file = $item . $path . '.php';
                if (file_exists($file)) {
                    require_once $file;
                }
            }
        }
        unset($item);
    });
}
