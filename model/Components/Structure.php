<?php

namespace is\Components;

use is\Helpers\System;
use is\Helpers\Strings;
use is\Helpers\Objects;
use is\Helpers\Matches;
use is\Helpers\Sessions;
use is\Helpers\Parser;
use is\Helpers\Paths;
use is\Components\Collection;

class Structure extends Collection
{
    public $extension;

    public function structure($array = null, $level = 0, $parents = [], $groups = null, $cache = null)
    {
        $parents_string = $parents ? Strings::join($parents, '/') . '/' : null;

        foreach ($array as $key => $item) {
            $k = Parser::fromString($key, ['simple' => null]);
            $type = System::typeOf($k[0], 'scalar') ? null : $k[0][1];
            $name = System::typeOf($k[0], 'scalar') ? $k[0] : $k[0][0];
            $sub = System::typeOf($item, 'iterable');

            $i = [
                //'name' => $parents ? Strings::join($parents, ':') . ':' . $name : $name,
                'name' => $name,
                'type' => $type,
                'parents' => $parents ? $parents : null,
                'data' => [
                    'name' => $name,
                    'groups' => $groups,
                    'template' => isset($k[1][0]) ? $k[1][0] : null,
                    'cache' => isset($k[2]) ? $k[2] : null,
                    'level' => $level,
                    'sub' => $sub,
                    'link' => null
                ]
            ];

            // special позволяет не делать обработку урла, что нужно для использования #... или ?..=..&..=..
            // чтобы убрать special нужно сделать более правильную обработку урла через helper
            // Paths::prepareUrl сейчас с этим не справляется, он не учитывает этот синтаксис

            $n = &$i['data']['cache'];
            if ($n) {
                if (Objects::len($n) === 1) {
                    $n = Objects::first($n, 'value');
                }
                if ($n === 'parent') {
                    $n = $cache;
                }
                if ($n) {
                    $cache = $n;
                }
            }
            unset($n);

            if ($type !== 'group') {
                $custom = System::typeOf($item, 'scalar');
                if ($custom) {
                    $i['type'] = 'custom';
                }
                $i['data']['link'] = $this->url($custom ? $item : $parents_string . $name . '/');
                $this->add($i);
            }

            //echo '<pre>' . print_r($i, 1) . '</pre>';

            if ($sub) {
                if ($type === 'group') {
                    $groups[] = $name;
                } else {
                    $level++;
                    $parents[] = $name;
                }

                $this->structure($item, $level, $parents, $groups, $cache);

                if ($type === 'group') {
                    $groups = Objects::unlast($groups);
                } else {
                    $level--;
                    $parents = Objects::unlast($parents);
                }
            }
        }
        unset($key, $item);
    }

    public function url($data)
    {
        $data = Paths::prepareUrl($data);

        if ($this->extension) {
            $last = Strings::last($data);
            if ($last === '/') {
                $data = Strings::unlast($data);
                $data .= $this->extension;
            }
        }

        return $data;
    }
}
