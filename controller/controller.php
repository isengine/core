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

// Launch uri
// Запускаем разбор uri

System::includes('uri:init', $path);

// Launch driver db
// Запускаем инициализацию драйвера базы данных

System::includes('driver:init', $path);

// Launch user
// Запускаем инициализацию пользователя

System::includes('user:init', $path);

// Launch language initialization
// Запускаем инициализацию языков

System::includes('language:init', $path);

// возможно, здесь не хватает инициализации языкового модуля и библиотек

// Launch fork
// Запускаем правила развилки

System::includes('init:fork', $path);

?>