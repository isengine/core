<?php defined('isPROCESS') or die;

# isCaptcha system element based on KCAPTCHA PROJECT VERSION 2.1

# Copyright by Kruglov Sergei, 2006, 2007, 2008, 2011, 2016
# www.captcha.ru, www.kruglov.ru

# System requirements: PHP 4.0.6+ w/ GD

# KCAPTCHA is a free software. You can freely use it for developing own site or software.
# If you use this software as a part of own sofware, you must leave copyright notices intact or add KCAPTCHA copyright notices to own.

# See config.php for customization

class isCaptcha {
	// generates keystring and image
	
	function __construct() {

		require 'config.php';
		require 'get.php';
		
		$this -> token = !empty($token) ? $token : null;
		
		$items = __DIR__ . DS . 'items' . DS;
		$fonts = [];
		if ($handle = opendir($items)) {
			while (false !== ($file = readdir($handle))) {
				if (preg_match('/\.png$/i', $file)) {
					$fonts[] = $items . $file;
				}
			}
		    closedir($handle);
		}	
	
		$alphabet_length = mb_strlen($alphabet);
		
		do {
			// generating random keystring
			while (true) {
				$this -> keystring = '';
				for ($i = 0; $i < $length; $i++) {
					$this -> keystring .= $symbols[mt_rand(0, strlen($symbols) - 1)];
				}
				if (!preg_match('/cp|cb|ck|c6|c9|rn|rm|mm|co|do|cl|db|qp|qb|dp|ww/', $this -> keystring)) break;
			}

			//font select
			if (!$select_font) {
				$font_file = $fonts[mt_rand(0, count($fonts) - 1)];
			} else {
				$font_file = $fonts[$select_font-1];
			}
			$font = imagecreatefrompng($font_file);
			imagealphablending($font, true);

			$fontfile_width = imagesx($font);
			$fontfile_height = imagesy($font) - 1;
			
			$font_metrics = [];
			$symbol = 0;
			$reading_symbol = false;

			// loading font
			for ($i = 0; $i < $fontfile_width && $symbol < $alphabet_length ; $i++) {
				$transparent = (imagecolorat($font, $i, 0) >> 24) == 127;

				if (!$reading_symbol && !$transparent) {
					$font_metrics[$alphabet[$symbol]] = ['start' => $i];
					$reading_symbol = true;
					continue;
				}

				if ($reading_symbol && $transparent) {
					$font_metrics[$alphabet[$symbol]]['end'] = $i;
					$reading_symbol = false;
					$symbol++;
					continue;
				}
			}

			$img = imagecreatetruecolor($width, $height);
			imagealphablending($img, true);
			$white = imagecolorallocate($img, 255, 255, 255);
			$black = imagecolorallocate($img, 0, 0, 0);

			imagefilledrectangle($img, 0, 0, $width - 1, $height - 1, $white);

			// draw text
			$x = 1;
			$odd = mt_rand(0, 1);
			if ($odd == 0) $odd = -1;
			for ($i = 0; $i < $length; $i++) {
				$m = $font_metrics[$this->keystring[$i]];
				$y = (($i % 2) * $amplitude - $amplitude / 2) * $odd
					+ mt_rand(-round($amplitude / 3), round($amplitude / 3))
					+ ($height - $fontfile_height) / 2;

				if ($no_spaces && !$rotate) {
					$shift = 0;
					if($i > 0) {
						$shift = 10000;
						for ($sy = 3; $sy < $fontfile_height - 10; $sy += 1) {
							for ($sx = $m['start'] - 1; $sx < $m['end']; $sx += 1) {
				        		$rgb = imagecolorat($font, $sx, $sy);
				        		$opacity = $rgb >> 24;
								if ($opacity < 127) {
									$left = $sx - $m['start'] + $x;
									$py = $sy + $y;
									if ($py > $height) break;
									for ($px = min($left, $width - 1); $px > $left - 200 && $px >= 0; $px -= 1) {
						        		$color = imagecolorat($img, $px, $py) & 0xff;
										if ($color + $opacity < 170) { // 170 - threshold
											if ($shift > $left - $px) {
												$shift = $left - $px;
											}
											break;
										}
									}
									break;
								}
							}
						}
						if ($shift == 10000) {
							$shift = mt_rand(4, 6);
						}

					}
				} else {
					$shift = 1;
				}
				
				if ($rotate) {
					
					// эксперименты с капчей:
					// 1. создать пустое изображение
					$tempimg = imagecreatetruecolor($m['end'] - $m['start'], $fontfile_height);
					imagealphablending($tempimg, true);
					$tempwhite = imagecolorallocate($tempimg, 255, 255, 255);
					imagefilledrectangle($tempimg, 0, 0, $m['end'] - $m['start'] - 1, $fontfile_height - 1, $tempwhite);
					
					// 2. скопировать изображение из исходного в пустое
					imagecopy($tempimg, $font, 0, 0, $m['start'], 1, $m['end']-$m['start'], $fontfile_height);
					
					// 3. повернуть изображение: $font = imagerotate($font, 2, 255);
					$tempimg = imagerotate($tempimg, mt_rand(-5,5)*5, 255);
					$tempx = round((imagesx($tempimg) - ($m['end']-$m['start'])) / 2);
					$tempy = round((imagesy($tempimg) - $fontfile_height) / 2);
					
					// 4. поставить изображение из пустого в новое
					imagecopy($img, $tempimg, $x - $shift, $y, $tempx, $tempy, $m['end'] - $m['start'], $fontfile_height);
					
				} else {
					imagecopy($img, $font, $x - $shift, $y, $m['start'], 1, $m['end'] - $m['start'], $fontfile_height);
				}
				
				$x += $m['end'] - $m['start'] - $shift;
			}
		} while ($x >= $width - 10); // while not fit in canvas

		//noise
		$white = imagecolorallocate($font, 255, 255, 255);
		$black = imagecolorallocate($font, 0, 0, 0);
		for ($i = 0; $i < (($height - 30) * $x) * $whitenoise; $i++) {
			imagesetpixel($img, mt_rand(0, $x - 1), mt_rand(10, $height - 15), $white);
		}
		for ($i = 0; $i < (($height - 30) * $x) * $blacknoise; $i++) {
			imagesetpixel($img, mt_rand(0, $x - 1), mt_rand(10, $height - 15), $black);
		}

		$center = $x / 2;

		//lines
		$linescol = imagecolorallocate($font, 127, 127, 127);
		if ($lines) {
			imageline($img, 0, round($height / 2), $width, round($height / 2), $linescol);
			
			if ($lines > 1) {
				for ($i = 1; $i < $lines; $i++) {
					imageline($img, 0, mt_rand(0, $height), $width, mt_rand(0, $height), $linescol);
				}
			}
		}
		if ($linenoise) {
			for ($i = 0; $i < $linenoise; $i++) {
				imageline($img, mt_rand(0, $width), mt_rand(0, $height), mt_rand(0, $width), mt_rand(0, $height), $linescol);
			}
		}

		// image
		$img2 = imagecreatetruecolor($width, $height);
		$foreground = imagecolorallocate($img2, $foreground_color[0], $foreground_color[1], $foreground_color[2]);
		$background = imagecolorallocate($img2, $background_color[0], $background_color[1], $background_color[2]);
		imagefilledrectangle($img2, 0, 0, $width - 1, $height - 1, $background);		
		imagefilledrectangle($img2, 0, $height, $width - 1, $height + 12, $foreground);

		if ($waves) {
			// periods
			$rand1 = mt_rand(750000, 1200000) / 10000000;
			$rand2 = mt_rand(750000, 1200000) / 10000000;
			$rand3 = mt_rand(750000, 1200000) / 10000000;
			$rand4 = mt_rand(750000, 1200000) / 10000000;
			// phases
			$rand5 = mt_rand(0, 31415926) / 10000000;
			$rand6 = mt_rand(0, 31415926) / 10000000;
			$rand7 = mt_rand(0, 31415926) / 10000000;
			$rand8 = mt_rand(0, 31415926) / 10000000;
			// amplitudes
			$rand9 = mt_rand(330, 420) / 110;
			$rand10 = mt_rand(330, 450) / 100;
		} else {
			$rand1 = $rand2 = $rand3 = $rand4 = $rand5 = $rand6 = $rand7 = $rand8 = $rand9 = $rand10 = 0;
		}

		//wave distortion
		for ($x = 0; $x < $width; $x++) {
			for ($y = 0; $y < $height; $y++) {
				$sx = $x + (sin($x * $rand1 + $rand5) + sin($y * $rand3 + $rand6)) * $rand9 - $width / 2 + $center + 1;
				$sy = $y + (sin($x * $rand2 + $rand7) + sin($y * $rand4 + $rand8)) * $rand10;

				if ($sx < 0 || $sy < 0 || $sx >= $width - 1 || $sy >= $height - 1) {
					continue;
				} else {
					$color = imagecolorat($img, $sx, $sy) & 0xFF;
					$color_x = imagecolorat($img, $sx+1, $sy) & 0xFF;
					$color_y = imagecolorat($img, $sx, $sy+1) & 0xFF;
					$color_xy = imagecolorat($img, $sx+1, $sy+1) & 0xFF;
				}

				if ($color == 255 && $color_x == 255 && $color_y == 255 && $color_xy == 255) {
					continue;
				} elseif ($color == 0 && $color_x == 0 && $color_y == 0 && $color_xy == 0) {
					$newred = $foreground_color[0];
					$newgreen = $foreground_color[1];
					$newblue = $foreground_color[2];
				} else {
					$frsx = $sx - floor($sx);
					$frsy = $sy - floor($sy);
					$frsx1 = 1 - $frsx;
					$frsy1 = 1 - $frsy;

					$newcolor = (
						$color * $frsx1 * $frsy1 +
						$color_x * $frsx * $frsy1 +
						$color_y * $frsx1 * $frsy +
						$color_xy * $frsx * $frsy
					);

					if ($newcolor > 255) $newcolor = 255;
					$newcolor = $newcolor / 255;
					$newcolor0 = 1 - $newcolor;

					$newred = $newcolor0 * $foreground_color[0] + $newcolor * $background_color[0];
					$newgreen = $newcolor0 * $foreground_color[1] + $newcolor * $background_color[1];
					$newblue = $newcolor0 * $foreground_color[2] + $newcolor * $background_color[2];
				}

				imagesetpixel($img2, $x, $y, imagecolorallocate($img2, $newred, $newgreen, $newblue));
			}
		}
		
		$this -> image = $img2;
		
	}

	// returns image
	function getImage() {
		
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		header('Cache-Control: no-store, no-cache, must-revalidate');
		header('Cache-Control: post-check=0, pre-check=0', false);
		header('Pragma: no-cache');
		if (function_exists('imagepng')) {
			header('Content-Type: image/x-png');
			imagepng($this -> image);
		} elseif (function_exists('imagejpeg')) {
			header('Content-Type: image/jpeg');
			imagejpeg($this -> image, null, $jpeg_quality);
		} elseif(function_exists('imagegif')) {
			header('Content-Type: image/gif');
			imagegif($this -> image);
		}
		
	}
	
	// returns keystring
	function getKeyString() {
		return $this -> keystring;
	}
	
}

?>