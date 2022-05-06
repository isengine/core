<?php

namespace is\Masters\Extenders\Device;

use is\Helpers\Sessions;
use is\Helpers\Parser;
use is\Helpers\Objects;
use is\Helpers\Strings;
use is\Helpers\Local;
use is\Helpers\System;
use is\Helpers\Matches;
use is\Helpers\Paths;
use is\Parents\Data;
use Detection\MobileDetect;

class Device extends Data
{
    public $type;
    public $os;
    public $screen;

    public function __construct()
    {
        $mobiledetect = new MobileDetect();

        $this->type = $mobiledetect->isMobile() ? ($mobiledetect->isTablet() ? 'tablet' : 'mobile') : 'desktop';

        if ($mobiledetect->isWindowsPhoneOS()) {
            $this->os = 'windowsphone';
        } elseif ($mobiledetect->isiOS()) {
            $this->os = 'ios';
        } elseif ($mobiledetect->isAndroidOS()) {
            $this->os = 'android';
        }

        unset($mobiledetect);
    }

    public function match($name, $compare)
    {
        return $compare && $this->$name === $compare;
    }
}
