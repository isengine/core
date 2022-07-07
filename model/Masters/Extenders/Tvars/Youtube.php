<?php

namespace is\Masters\Extenders\Tvars;

use is\Helpers\System;
use is\Helpers\Objects;

class Youtube extends Master
{
    public function launch($data)
    {
        $data = Objects::createByIndex(
            [0, 1, 2],
            $data
        );

        $target = $data[1];
        if ($target === 'image') {
            $type = System::set($data[2]) ? $data[2] : 'maxresdefault';
            return 'https://img.youtube.com/vi/' . $data[0] . '/' . $type . '.jpg';
        } elseif ($target === 'link') {
            return 'https://www.youtube.com/watch?v=' . $data[0];
        } elseif ($target === 'lazy') {
            $width = System::set($data[1]) ? $data[1] : '100%';
            $height = System::set($data[2]) ? $data[2] : '100%';
            return
                '<iframe
                    width="' . $width . '"
                    height="' . $height . '"
                    src="https://www.youtube.com/embed/' . $data[0] . '"
                    srcdoc="
                        <style>*{padding:0;margin:0;overflow:hidden}
                        html,body{height:100%}
                        img,span{position:absolute;width:100%;top:0;bottom:0;margin:auto}
                        span{height:1.5em;text-align:center;font:48px/1.5 sans-serif;color:white;text-shadow:0 0 0.5em black}
                        </style>
                        <a href=https://www.youtube.com/embed/' . $data[0] . '>
                        <img src=https://img.youtube.com/vi/' . $data[0] . '/hqdefault.jpg alt=\'YouTube Video\'>
                        <span>▶</span>
                        </a>
                    "
                    title="YouTube Video"
                    frameborder="0"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen
                ></iframe>';
        } else {
            $width = System::set($data[1]) ? $data[1] : '100%';
            $height = System::set($data[2]) ? $data[2] : '100%';
            return
                '<iframe
                    width="' . $width . '"
                    height="' . $height . '"
                    src="https://www.youtube.com/embed/' . $data[0] . '"
                    title="YouTube Video"
                    frameborder="0"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen
                ></iframe>';
        }
    }
}
