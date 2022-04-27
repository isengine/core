<?php

namespace is\Components;

use is\Helpers\System;
use is\Helpers\Strings;
use is\Helpers\Objects;

header(System::server('SERVER_PROTOCOL', true) . ' 200 OK', true, 200);
header('Content-Type: text/html; charset=UTF-8');

?><!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no" />
    <meta name="robots" content="noindex, nofollow" />
    <title>isENGINE system info</title>
</head>
<body>

<div>
<center>

    <p><strong>System Info</strong></p>
    <p>
        Platform: isENGINE
        <br>
        Version: <?= isENGINE; ?>
        <br>
        Server: <?= System::server('SERVER_SOFTWARE', true); ?>
        <br>
        Date: <?= date('d.m.Y', filemtime(System::server('SCRIPT_FILENAME', true))); ?>
    </p>
    <p>project on github:<br><a href="https://github.com/isengine/" target="_blank">https://github.com/isengine/</a></p>

</center>
</div>

</body>
</html><?php exit; ?>