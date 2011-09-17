<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');


$_EXTCONF = unserialize($_EXTCONF);    // unserializing the configuration so we can use it here
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$_EXTKEY]['useTTF'] = intval($_EXTCONF['useTTF']) ? 1 : 0;

$GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$_EXTKEY]['imgHeight'] = intval($_EXTCONF['imgHeight']) ? intval($_EXTCONF['imgHeight']) : 25;
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$_EXTKEY]['imgWidth'] = intval($_EXTCONF['imgWidth']) ? intval($_EXTCONF['imgWidth']) : 95;
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$_EXTKEY]['captchaChars'] = intval($_EXTCONF['captchaChars']) ? intval($_EXTCONF['captchaChars']) : 5;
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$_EXTKEY]['noNumbers'] = intval($_EXTCONF['noNumbers']) ? 1 : 0;
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$_EXTKEY]['noLower'] = intval($_EXTCONF['noLower']) ? 1 : 0;
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$_EXTKEY]['noUpper'] = intval($_EXTCONF['noUpper']) ? 1 : 0;

$GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$_EXTKEY]['letterSpacing'] = intval($_EXTCONF['letterSpacing']) ? intval($_EXTCONF['letterSpacing']) : 16;
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$_EXTKEY]['fontSize'] = intval($_EXTCONF['fontSize']) ? intval($_EXTCONF['fontSize']) : 16;
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$_EXTKEY]['fontFile'] = trim($_EXTCONF['fontFile']);

$GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$_EXTKEY]['bold'] = intval($_EXTCONF['bold']) ? 1 : 0;
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$_EXTKEY]['angle'] = intval($_EXTCONF['angle']) ? intval($_EXTCONF['angle']) : 20;
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$_EXTKEY]['diffx'] = intval($_EXTCONF['diffx']) ? intval($_EXTCONF['diffx']) : 0;
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$_EXTKEY]['diffy'] = intval($_EXTCONF['diffy']) ? intval($_EXTCONF['diffy']) : 2;
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$_EXTKEY]['xpos'] = intval($_EXTCONF['xpos']) ? intval($_EXTCONF['xpos']) : 3;
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$_EXTKEY]['ypos'] = intval($_EXTCONF['ypos']) ? intval($_EXTCONF['ypos']) : 4;
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$_EXTKEY]['noises'] = intval($_EXTCONF['noises']) ? intval($_EXTCONF['noises']) : 6;

$GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$_EXTKEY]['backcolor'] = trim($_EXTCONF['backcolor']) ? trim($_EXTCONF['backcolor']) : '#f4f4f4';
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$_EXTKEY]['textcolor'] = trim($_EXTCONF['textcolor']) ? trim($_EXTCONF['textcolor']) : '#000000';
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$_EXTKEY]['obfusccolor'] = trim($_EXTCONF['obfusccolor']) ? trim($_EXTCONF['obfusccolor']) : '#c0c0c0';
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$_EXTKEY]['excludeChars'] = trim($_EXTCONF['excludeChars']) ? trim($_EXTCONF['excludeChars']) : '';


?>
