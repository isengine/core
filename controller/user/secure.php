<?php

namespace is;

use is\Helpers\System;
use is\Helpers\Strings;
use is\Helpers\Objects;
use is\Helpers\Sessions;
use is\Helpers\Prepare;
use is\Helpers\Ip;
use is\Components\Session;
use is\Components\Uri;
use is\Components\State;
use is\Components\Config;
use is\Components\Display;
use is\Components\Log;
use is\Components\User;
use is\Masters\Database;

// читаем user

$user = User::getInstance();
$state = State::getInstance();
$session = Session::getInstance();

//echo '**';
//System::debug($session);

// обработчик ошибок при проверке

// пока здесь только отчеты в логах, ну кроме ошибки за бан
// нужно сделать вывод или отправку уведомлений пользователю
// а также механизм добавления новых записей в базу данных
// и подтверждения у пользователя на их добавление с уведомлением по e-mail,
// вводом капчи - ну в общем как положено
// возможно, это придется сделать через шаблон ошибки или через шаблон восстановления доступа

if ($user->getFieldsBySpecial('ban')) {
    // проверка на бан

    $session->close();

    $state->set('error', 403);
    $state->set('reason', 'security user verification - user are banned');
} else {
    $allow = [
        'ip' => null,
        'agent' => null,
        'user_ip' => $user->getFieldsBySpecial('ips'),
        'user_agent' => $user->getFieldsBySpecial('agents'),
        'session_ip' => $session->getSession('ip'),
        'session_agent' => $session->getSession('agent'),
    ];

    // проверка на присутствие текущего ip в списке разрешенных

    if (Ip::range($allow['session_ip'], $allow['user_ip'])) {
        $allow['ip'] = true;
    }

    // проверка на присутствие текущего хэша агента в списке разрешенных

    if (
        is_array($allow['user_agent'])
        && in_array($allow['session_agent'], $allow['user_agent'])
    ) {
        $allow['agent'] = true;
    }

    if ($allow['ip'] && !$allow['agent']) {
        //logging('security user verification - unknown agent but known ip, agent will be added in list');
        //echo 'security user verification - unknown agent but known ip, agent will be added in list';

        $user->addFieldsBySpecial('allowagent', $allow['session_agent']);

        // сюда не хватает записи о перезаписи $user->allow['allowagent'] в базу данных
        // он уже массив, так что никаких дополнительных условий делать не нужно
        // разве только узнать имя поля в базе данных пользователя
    } elseif (!$allow['ip'] && $allow['agent']) {
        //$l = '
        //  security user verification -
        //  unknown ip but known agent, ip will be added in list with extended diapason
        //';
        //logging($l);
        //echo $l;

        $user->addFieldsBySpecial('allowip', $allow['session_ip']);

        // сюда не хватает записи о перезаписи $user->allow['allowip'] в базу данных
        // он уже массив, так что никаких дополнительных условий делать не нужно
        // разве только узнать имя поля в базе данных пользователя
    } elseif (!$allow['ip'] && !$allow['agent']) {
        //$l = '
        //  security user verification -
        //  unknown ip and agent, user must be notified and added this in lists
        //';
        //logging($l);
        //echo $l;
    }

    unset($allow);
}
