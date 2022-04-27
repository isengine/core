<?php

namespace is;

use is\Helpers\System;
use is\Helpers\Strings;
use is\Helpers\Objects;
use is\Helpers\Sessions;
use is\Helpers\Prepare;
use is\Components\Session;
use is\Components\Uri;
use is\Components\State;
use is\Components\Config;
use is\Components\Display;
use is\Components\Log;
use is\Masters\Api;

// читаем uri

$api = Api::getInstance();
$api->launch();

$print = Display::getInstance();
$print->dump($api);

// Здесь мы устанавливаем правила окончания обработки api
// Например, задаем заголовок или передаем состояние ошибки
// Завершаем обработку по exit
// или продолжаем, если нужно вывести страницу ошибки
// Или делаем релоад на урл с новыми параметрами, например после обработки формы

// но сейчас только:
exit;
