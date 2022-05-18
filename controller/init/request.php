<?php

namespace is;

use is\Helpers\Objects;
use is\Helpers\Strings;
use is\Helpers\Sessions;
use is\Helpers\Paths;
use is\Helpers\System;
use is\Helpers\Parser;
use is\Helpers\Matches;
use is\Helpers\Local;
use is\Components\Session;
use is\Components\Config;
use is\Components\State;

// читаем сессию

$config = Config::getInstance();
$session = Session::getInstance();
$state = State::getInstance();

$origin = $session->get('origin', 'key');
$referrer = $origin ? $origin : $session->get('referrer', 'key');
$server = '//' . System::server('host');
$secure = $config->get('secure:referrer');

$isreferrer = true;

if ($referrer && !Strings::match($referrer, $server)) {
    // проверяем запросы по списку

    if ($secure) {
        $file = DR . 'config' . DS . 'referrer.' . $secure . '.ini';
        //echo $file;

        $content = Local::readFile($file);
        $content = Parser::fromJson($content);
        //$this->mergeData(['content' => $this->content], true);

        if ($content) {
            $match = Matches::maskOf(
                Paths::parseUrl($referrer, 'host'),
                $content
            );

            if (
                ($secure === 'blacklist' && $match)
                || ($secure === 'whitelist' && !$match)
            ) {
                $isreferrer = false;
            }
        } else {
            $isreferrer = false;
        }
    }
} elseif (!$session->get('agent', 'key')) {
    $isreferrer = false;
}

// Проверяем разрешения на запросы с других сайтов

$request = $config->get('secure:request');
$method = $session->get('request', 'key');

// разрешено все или разрешено то, что указано

$isrequest = $config->get('secure:request') === true || Strings::match($request, $method);

// определяем, хороший или плохой запрос
// плохие запросы - запрещенные и из сторонних источников

$state->set('request', $isrequest || $isreferrer);

if (!$state->get('request')) {
    $state->set('error', 403);
    $state->set('reason', 'it was a forbidden request - not allowed method or from not allowed referrer');
}

// Вы можете ограничить запросы следующим способом
// Задав только разрешенные запросы в secure:request, например get
// Задав метод проверки реферреров в secure:referrer, например whitelist
// И внеся в этот whitelist в корне сайта список разрешенных реферреров
