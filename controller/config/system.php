<?php

namespace is;

use is\Components\Config;

// задаем конфигурацию php

$config = Config::getInstance();

// принудительно устанавливаем имя идентификатора сессии
// т.к. например в nginx он отказывается его принимать в конфиге

ini_set('session.name', $config->get('system:session'));

// вывод ошибок рекомендуется включать только на время разработки

if ($config->get('develop:enable')) {
    ini_set('display_errors', 'on');
    ini_set('display_startup_errors', true);

    $result = $config->get('develop:errors') === true ? E_ALL : 0;
    if ($config->get('develop:errors') !== true) {
        //$er = 'all:!notice:!strict';
        //$er = 'E_ALL & ~E_NOTICE & ~E_STRICT';

        $errors = preg_split(
            '/\:/u',
            preg_replace(
                ['/\s/u', '/\&/u', '/\^/u', '/\~/u'],
                ['', ':', ':!', '!'],
                $config->get('develop:errors')
            )
        );

        foreach ($errors as $key => $item) {
            $sign = $item[0] === '!';

            if ($sign) {
                $item = mb_substr($item, 1);
            }
            $item = mb_strtoupper($item);

            if ($item[0] !== 'E') {
                $item = 'E_' . $item;
            }

            $item = constant($item);
            $result = !$key ? $item : ($sign ? $result & ~$item : $result & $item);
            //echo $item . '<br>';
        }
        unset($item);
    }

    ini_set('error_reporting', $result);
    //echo $result;
} else {
    ini_set('display_errors', 'off');
    ini_set('display_startup_errors', false);
    ini_set('error_reporting', 0);
}

// дополнительные установки локали

$charset = $config->get('system:charset');

ini_set('default_charset', $charset);

if (version_compare(PHP_VERSION, '5.6.0', '<') && function_exists('mb_internal_encoding')) {
    mb_internal_encoding($charset);
}
if (function_exists('mb_regex_encoding')) {
    mb_regex_encoding($charset);
}

unset($charset);

// установка часового пояса

$timezone = $config->get('default:timezone');

if ($timezone) {
    date_default_timezone_set($timezone);
}

unset($timezone);
