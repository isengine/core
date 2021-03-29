<?php

// Рабочее пространство имен

namespace is;

use is\Helpers\System;

// Create including components
// Создаем подключение компонентов

$path = __DIR__;

// Pre-check for blocking by ip
// Предварительная проверка на блокировку по ip

System::includes('init:blockip', $path);

// Launch system configuration
// Запускаем конфигурацию системы

System::includes('init:config', $path);

// Launch logs
// Запускаем логи

System::includes('init:log', $path);

// Launch error
// Запускаем компонент ошибки

System::includes('init:error', $path);

// Launch session set and check
// Запускаем установку и проверку сессии

System::includes('init:session', $path);

// Launch check query
// Запускаем проверку качества запроса

System::includes('init:request', $path);

// Launch check cookie
// Запускаем проверку работы куки

System::includes('init:cookie', $path);

// если есть ошибка, нет смысла что-либо разбирать
//$state = Model\Components\State::getInstance();
//if (!$state -> get('error')) {
//}

// Launch uri
// Запускаем разбор uri

System::includes('init:uri', $path);

// Launch driver db
// Запускаем инициализацию драйвера базы данных

System::includes('init:driver', $path);

// Launch api
// Запускаем api

System::includes('init:api', $path);

// Launch user
// Запускаем инициализацию пользователя

System::includes('init:user', $path);

// Launch language initialization
// Запускаем инициализацию языков

System::includes('init:language', $path);

// возможно, здесь не хватает инициализации языкового модуля и библиотек

// Launch routing
// Запускаем правила роутинга

System::includes('init:router', $path);

// Launch display buffer
// Запускаем буфер вывода на экран

System::includes('init:display', $path);

// Include view
// Подключаем вид, шаблонизатор

System::includes('init:view', $path);

?>