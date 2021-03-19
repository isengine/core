<?php

// Рабочее пространство имен

namespace is;

use is\Helpers\System;
use is\Helpers\Strings;
use is\Helpers\Objects;
use is\Helpers\Sessions;
use is\Helpers\Prepare;
use is\Model\Components\Path;
use is\Model\Components\Session;
use is\Model\Components\Uri;
use is\Model\Components\State;
use is\Model\Components\Config;
use is\Model\Components\Content;
use is\Model\Components\Display;
use is\Model\Components\Log;
use is\Model\Apis\Api;

// читаем uri

$api = Api::getInstance();
$api -> launch();

$print = Display::getInstance();
$print -> dump($api);

// Здесь мы устанавливаем правила окончания обработки api
// Например, задаем заголовок или передаем состояние ошибки
// Завершаем обработку по exit
// или продолжаем, если нужно вывести страницу ошибки
// Или делаем релоад на урл с новыми параметрами, например после обработки формы

// но сейчас только:
exit;

?>