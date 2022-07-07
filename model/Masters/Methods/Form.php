<?php

namespace is\Masters\Methods;

use is\Helpers\Local;
use is\Helpers\Strings;
use is\Helpers\Objects;
use is\Helpers\Parser;
use is\Helpers\System;
use is\Helpers\Prepare;
use is\Helpers\Paths;
use is\Helpers\Sessions;
use is\Helpers\Matches;
use is\Components\Config;
use is\Components\State;
use is\Components\Uri;
use is\Masters\Module;

class Form extends Master
{
    public $settings;
    public $errors;

    public function launch()
    {
    }

    public function settings($field = 'instance')
    {
        $instance = Parser::fromString($this->getData($field));

        if (
            !$instance
            || !System::typeIterable($instance)
            || Objects::len($instance) < 3
        ) {
            return;
        }

        $config = Config::getInstance();
        $state = State::getInstance();
        $module = Module::getInstance();

        $module->init(
            $config->get('path:vendors'),
            $config->get('path:app') . 'Masters' . DS . 'Modules' . DS,
            //$config->get('path:cache') . 'modules' . DS,
            //$config->get('cache:modules')
            null,
            null
        );

        $module->launch(
            $instance[1] . ':' . $instance[0],
            Strings::join(Objects::get($instance, 2), ':') . '|' . 'settings'
        );

        unset($module);

        $sets = Parser::fromJson($state->get('form'));

        if ($sets && is_array($sets)) {
            $this->settings = $sets;
        }
    }

    public function fields()
    {
        Objects::each($this->settings, function ($item, $key) {
            $name = $key;
            $options = &$item['options'];
            $value = $this->getData($name);
            $change = null;

            if (
                System::typeIterable($item['data'])
                && !empty($value)
            ) {
                $change = true;
                $value = $item['data'][$value];
            }

            if (
                $value
                && !$change
                && !empty($options['filter'])
            ) {
                $filter = Strings::split($options['filter'], ':');
                foreach ($filter as $i) {
                    if (Strings::match($value, $i)) {
                        $change = true;
                        $value = Strings::replace($value, $i);
                        $this->error($name, 'filter');
                    }
                }
                unset($i);
            }

            if (
                $value
                && !empty($options['prepare'])
            ) {
                $change = true;
                $value = $this->prepareFields($value, $options['prepare']);
            }

            if (
                $value
                && !empty($options['validate'])
            ) {
                $verify = $this->prepareFields($value, $options['validate']);
                if ($value !== $verify) {
                    $change = true;
                    $value = $verify;
                    $this->error($name, 'validate');
                }
                unset($verify);
            }

            if (
                $value
                && !empty($item['min'])
                && $value < $item['min']
            ) {
                $change = true;
                $value = $item['min'];
                $this->error($name, 'min');
            }

            if (
                $value
                && !empty($item['max'])
                && $value > $item['max']
            ) {
                $change = true;
                $value = $item['max'];
                $this->error($name, 'max');
            }

            if (
                $value
                && !empty($item['minlength'])
                && Strings::len($value) < $item['minlength']
            ) {
                $this->error($name, 'minlength');
            }

            if (
                $value
                && !empty($item['maxlength'])
                && Strings::len($value) > $item['maxlength']
            ) {
                $change = true;
                $value = Strings::get($value, 0, $item['maxlength']);
                $this->error($name, 'maxlength');
            }

            if (
                $value
                && !empty($options['match'])
            ) {
                if (!Matches::regexpOf($value, $options['match'], true)) {
                    $this->error($name, 'match');
                }
            }

            if (
                !System::set($value)
                && $item['required']
            ) {
                $this->error($name, 'required');
            }

            if ($change) {
                $this->setData($name, $value);
            }

            return $item;
        });
    }

    public function prepareFields($result, $prepare)
    {
        $prepare = Objects::convert($prepare);

        Objects::each($prepare, function ($item) use (&$result) {
            if ($item) {
                if (Strings::match($item, '.')) {
                    $second = Strings::after($item, '.');
                    $item = Strings::before($item, '.');
                    $result = Prepare::$item($result, $second);
                } else {
                    $result = Prepare::$item($result);
                }
            }
        });

        return $result;

        //foreach ($prepare as $item) {
        //    if (Strings::match($item, '.')) {
        //        $data = Strings::after($item, '.');
        //        $item = Strings::before($item, '.');
        //        $result = Prepare::$item($result, $data);
        //    } else {
        //        $result = Prepare::$item($result);
        //    }
        //
        //    $data = Prepare::$item($data);
        //}
        //unset($item);
        //return $data;
    }

    public function spam($field = 'spam', $break = null)
    {
        // определение спам-ботов по заполнению скрытого поля,
        // которое должно присутствовать в форме,
        // но должно оставаться пустым

        // возвращает результат проверки
        // а что делать с этим результатом - ваша реализация

        // если второй аргумент задан, то он считается кодом ошибки
        // при этом, если спам был обнаружен, выводится ошибка
        // и дальнейшие операции прерываются

        $field = $this->getData($field);
        $spam = $field === null || System::set($field);
        // раньше было !isset($field)
        // условие странное, потому что $field уже будет задан
        // но нам нужна проверка на совершенно пустое значение, даже не 0 и ''
        if ($break && $spam) {
            $this->break($break);
        }
        return $spam;
    }

    public function error($field, $message = null)
    {
        // запись ошибки с указанием имени поля в качестве ключа
        // если описании пустое, то в значение тоже будет записано имя поля

        $this->errors[$field][] = $message ? $message : $field;
    }

    public function errors()
    {
        // проверка на наличие ошибок
        return System::typeIterable($this->errors);
    }

    public function reload($string = null)
    {
        if (!$string) {
            $string = Sessions::getCookie('previous-url');
        }
        Sessions::reload($string);
    }

    public function returns($field = 'success', $link = null)
    {
        // возвращает url-адрес, содержащий данные из формы для вставки обратно
        // аргументы по большей части чисто служебные
        // первый аргумент добавляет поле, определяющее успешное выполнение запроса
        // если второй аргумент задан, то страница перезагружается по этому url-адресу

        $config = Config::getInstance();

        $array = [];
        //$string = Sessions::getCookie('previous-url');
        $string = Sessions::getCookie('current-url');

        // раньше работали с current-url, очевидно из-за того, что
        // система не перезаписывала current и previous при перезагрузке с запросом по api,
        // с одной стороны, так в общем-то и должно быть, чтобы запросы не сохранялись
        // с другой стороны, помнить прямой api-запрос правильно, раз уж он был

        if (!$string) {
            Sessions::reload();
        }

        if (Strings::match($string, '?')) {
            $array = Paths::querySplit($string);
            $string = Strings::before($string, '?');
        }

        if ($link) {
            $string .= Strings::replace($link, ':', '/') . '/';
        }

        $keys = Objects::keys($this->getData());
        $array = Objects::removeByIndex($array, $keys);
        if ($field) {
            unset($array[$field]);
        }

        if ($this->errors()) {
            Objects::each(Objects::keys($this->errors), function ($item) use (&$array) {
                //$array[$item] = Prepare::urlencode($this->getData($item));
                $array[$item] = $this->getData($item);
            });
        } elseif ($field) {
            $array[$field] = true;
        }

        if ($config->get('url:query')) {
            $string .= Paths::queryJoin($array);
        } else {
            $string .=
                System::type($config->get('url:rest'), 'numeric')
                ? ''
                : (Strings::last($string) === '/' ? '' : '/') . $config->get('url:rest') . '/';
            $string .= $string .= Paths::restJoin($array);
            // это никакая не ошибка?
        }

        Sessions::reload($string);
        //return $string;
    }

    public function break($code = 403)
    {
        Sessions::setHeaderCode($code);
        exit;
    }
}
