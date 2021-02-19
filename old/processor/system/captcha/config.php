<?php defined('isPROCESS') or die;

# isCaptcha configuration file

# do not change without changing font files!
$alphabet = '0123456789abcdefghijklmnopqrstuvwxyz';

# symbols used to draw isCaptcha
$symbols = 'alphanumeric';

# isCaptcha string length
$font = false;

# isCaptcha string length
$length = 6;

# isCaptcha image size (you do not need to change it, this parameters is optimal)
$width = 160;
$height = 60;

# symbol's vertical fluctuation amplitude
$amplitude = 8;
$waves = true;
$rotate = false;

#noise
$blacknoise = 1/64;
$whitenoise = 1/8;
$linenoise = 0;

#lines
$lines = 2;

# increase safety by prevention of spaces between symbols
$no_spaces = true;

# isCaptcha image colors
$color = '#00000';
$bgcolor = '#FFFFFF';

# JPEG quality of isCaptcha image (bigger is better quality, but larger file size)
$jpeg_quality = 75;

?>