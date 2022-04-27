<?php

namespace is;

use is\Helpers\System;

$default = [
    'system' => [
        'php' => '5.6',
        'session' => 'SID',
        'charset' => 'UTF-8',
        'local' => '127.0.0.1',
        'sender' => 'X-Mailer: PHP/' . phpversion()
    ],
    'time' => [
        'minute' => 60,
        'hour' => 3600,
        'day' => 86400,
        'week' => 604800,
        'month' => 2628000,
        'year' => 31556926
    ],
    'path' => [
    ],
    'api' => [
        'server' => null,
        'name' => 'api',
        'key' => 'key',
        'token' => 'token'
    ],
    'error' => [
        'prefix' => '/error/',
        'postfix' => '',
        'template' => 'error',

        //'prefix' => '/e',
        //'postfix' => '.html'

        //'prefix' => '?error=',
        //'postfix' => ''
    ],
    'url' => [
        'rest' => 'data',
        'keys' => true,
        'query' => true
    ],
    'router' => [
        'folders' => [
            'convert' => null,
            'index' => null,
            'extension' => null
        ],
        'files' => [
            'convert' => null,
            'index' => null,
            'extension' => null
        ],
        'convert' => [
            'from' => null,
            'to' => null
        ],
        'index' => null,
        'reload' => true
    ]
];
