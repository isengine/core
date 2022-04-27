<?php

namespace is;

use is\Helpers\System;
use is\Helpers\Sessions;
use is\Helpers\Paths;
use is\Components\State;
use is\Components\Session;

// читаем сессию

$state = State::getInstance();

if (Sessions::getCookie('isengine')) {
    $state->set('cookie', true);
} else {
    // работа алгоритма ниже достаточно относительна,
    // т.к. он будет корректно работать только при переинициализации страницы
    // но он позволяет не делать многократной переинициализации сессии, ajax запросы и т.п.
    // в дополнение к нему, существует блок check, который срабатывает, если !state/cookie
    // он пробует проинициализировать куки и скрипты и выводит сообщение об ошибке

    $state->set('cookie', false);
    $time = (new \DateTime())->getTimestamp();
    Sessions::setCookie('isengine', $time);
}
