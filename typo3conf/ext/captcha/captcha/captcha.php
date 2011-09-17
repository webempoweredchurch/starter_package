<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2005-2006 Kraft Bernhard (kraftb@kraftb.at)
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
/**
 * Captcha library code.
 *
 * $Id$
 *
 * @author	Kraft Bernhard <kraftb@kraftb.at>
 */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 */


session_start();

define('TYPO3_MODE', 'FE');
define(PATH_this, dirname(__FILE__).'/');
define(PATH_site, dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/');
define(PATH_typo3conf, PATH_site.'typo3conf/');

require_once(PATH_typo3conf.'localconf.php');
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['captcha'] = unserialize($TYPO3_CONF_VARS['EXT']['extConf']['captcha']); 


function typo3_distortString($img, $text, $tcolor, $bcolor, $angle, $diffx, $diffy, $xpos, $ypos, $letterSpacing, $bold, $fontSize, $fontFile, $useTTF = 0)	{
		// When no TTF get's used.
	$charx = 20;
	$chary = 20;
	$osx = 5;
	$osy = 0;

	$fg = imagecolorallocate($img, $tcolor[0],$tcolor[1],$tcolor[2]);
	$bg = imagecolorallocate($img, $bcolor[0],$bcolor[1],$bcolor[2]);

	for ($x = 0; $x < strlen($text); $x++)	{
		$c = substr($text, $x, 1);
		$da = rand(0, $angle*2)-$angle;
		$dx = intval(rand(0,$diffx*2)-$diffx);
		$dy = intval(rand(0,$diffy*2)-$diffy);
		if ($useTTF)	{
			$ret = imagettftext($img, $fontSize, $da, $xpos+$dx, $ypos+$dy+$fontSize, $fg, $fontFile, $c);
			if ($bold)	{
				$ret = imagettftext($img, $fontSize, $da, $xpos+$dx+1, $ypos+$dy+$fontSize, $fg, $fontFile, $c);
				$ret = imagettftext($img, $fontSize, $da, $xpos+$dx+1, $ypos+$dy+$fontSize+1, $fg, $fontFile, $c);
				$ret = imagettftext($img, $fontSize, $da, $xpos+$dx, $ypos+$dy+$fontSize+1, $fg, $fontFile, $c);
			}
			$xpos += ($ret[2]-$ret[0]);
		} else	{
			$tmpi = imagecreate($charx, $chary);
			$back = imagecolorallocate($tmpi, $bcolor[0], $bcolor[1], $bcolor[2]);
			$fcol = imagecolorallocate($tmpi, $tcolor[0], $tcolor[1], $tcolor[2]);
			imagefill($tmpi, 0, 0, $back);
			imagestring($tmpi, 5, $osx, $osy, $c, $fcol);
			$rot = imagerotate($tmpi, $da, $back);
			$rback = imagecolorclosest($rot, $bcolor[0], $bcolor[1], $bcolor[2]);
			imagecolortransparent($rot, $rback);
			imagecopymerge($img, $rot, $xpos+$dx, $ypos+$dy, 0, 0, 20, 20, 100);
		}
		$xpos += $letterSpacing;
	}	
}



function typo3_noiseBackground($img_number, $obfucolor, $w, $h, $noises = 5)	{
	for ($x = 0; $x < $noises; $x++)	{
		$cx = rand(0, $w);
		$cy = rand(0, $h);
		$cw = rand($w/5, $w);
		$ch = rand($h/5, $h);
		imageellipse($img_number, $cx, $cy, $cw, $ch, $obfucolor);
		imageellipse($img_number, $cx+1, $cy, $cw, $ch, $obfucolor);
		imageellipse($img_number, $cx+1, $cy+1, $cw, $ch, $obfucolor);
		imageellipse($img_number, $cx, $cy+1, $cw, $ch, $obfucolor);
	}
}

function typo3_strrand($len, $digits, $exclude_chars = '')	{
	$all = array();
	foreach ($digits as $range)	{
		$all = array_merge($all, range($range[0], $range[1]));
	}
	for ($x = 0; $x < strlen($exclude_chars); $x++)	{
		if ( ($pos = array_search(ord($exclude_chars[$x]), $all))!==false )	{
			unset($all[$pos]);
		}
	}
	$final = array();
	$c = 0;
	foreach ($all as $x => $v)	{
		$final[$c++] = $v;
	}
	$str = '';
	for ($x = 0; $x < $len; $x++)		{
		$r = rand(0, count($final)-1);
		$str .= chr($final[$r]);
	}
	return $str;
}
function typo3_getColor($color, $default = 0)	{
	$back_r = $default;
	$back_g = $default;
	$back_b = $default;
	if ($color = trim($color))	{
		if (substr($color, 0, 1)=='#')	{
			$color = substr($color, 1);
		}
		if (strlen($color)==3)	{
			$back_r = hexdec(substr($color, 0, 1).substr($color, 0, 1));
			$back_g = hexdec(substr($color, 1, 1).substr($color, 1, 1));
			$back_b = hexdec(substr($color, 2, 1).substr($color, 2, 1));
		} elseif (strlen($color)==6)	{
			$back_r = hexdec(substr($color, 0, 2));
			$back_g = hexdec(substr($color, 2, 2));
			$back_b = hexdec(substr($color, 4, 2));
		}
	}
	return array($back_r, $back_g, $back_b);
}

$eChars = trim($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['captcha']['excludeChars']);
$imgHeight = intval($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['captcha']['imgHeight']);
$imgWidth = intval($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['captcha']['imgWidth']);
$captchaChars = intval($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['captcha']['captchaChars']);
list($bg_r, $bg_g, $bg_b) = $bga = typo3_getColor($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['captcha']['backcolor'], 244);
list($tc_r, $tc_g, $tc_b) = $tca = typo3_getColor($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['captcha']['textcolor'], 0);
list($ob_r, $ob_g, $ob_b) = typo3_getColor($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['captcha']['obfusccolor'], 180);
$angle = intval($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['captcha']['angle']);
$diffx = intval($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['captcha']['diffx']);
$diffy = intval($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['captcha']['diffy']);
$xpos = intval($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['captcha']['xpos']);
$ypos = intval($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['captcha']['ypos']);
$useTTF = intval($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['captcha']['useTTF']);
$bold = intval($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['captcha']['bold']);
$letterSpacing = intval($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['captcha']['letterSpacing']);
$fontSize = intval($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['captcha']['fontSize']);
$fontFile= $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['captcha']['fontFile']?PATH_site.$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['captcha']['fontFile']:PATH_this.'vera.ttf';
$noises = intval($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['captcha']['noises']);


$R_NUMBERS = array(ord('0'), ord('9'));
$R_LOWER_CHARS = array(ord('a'), ord('z'));
$R_UPPER_CHARS = array(ord('A'), ord('Z'));

$RA_CHARS = array();
if (!intval($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['captcha']['noNumbers']))	{
	$RA_CHARS[] = $R_NUMBERS;
}
if (!intval($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['captcha']['noLower']))	{
	$RA_CHARS[] = $R_LOWER_CHARS;
}
if (!intval($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['captcha']['noUpper']))	{
	$RA_CHARS[] = $R_UPPER_CHARS;
}
if (!count($RA_CHARS))	{
	die('No characters configured');
}

$text = $_SESSION['tx_captcha_string'] = typo3_strrand($captchaChars, $RA_CHARS, $eChars);
$imgObj = imagecreate($imgWidth, $imgHeight);
$backcolor = imagecolorallocate($imgObj, $bg_r, $bg_g, $bg_b);
$textcolor = imagecolorallocate($imgObj, $tc_r, $tc_g, $tc_b);
$obfucolor = imagecolorallocate($imgObj, $ob_r, $ob_g, $ob_b);

imagefill($imgObj, 0, 0, $backcolor);

typo3_noiseBackground($imgObj, $obfucolor, $imgWidth, $imgHeight, $noises);
typo3_distortString($imgObj, $text, $tca, $bga, $angle, $diffx, $diffy, $xpos, $ypos, $letterSpacing, $bold, $fontSize, $fontFile, $useTTF);

header("Content-type: image/png");
imagepng($imgObj);

?>
