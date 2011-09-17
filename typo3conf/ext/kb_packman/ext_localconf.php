<?php
if (!defined ("TYPO3_MODE")) 	die ("Access denied.");

$TYPO3_CONF_VARS["BE"]["unzip"]["list-zip"]["pre_lines"] = 3;
$TYPO3_CONF_VARS["BE"]["unzip"]["list-zip"]["post_lines"] = 2;
$TYPO3_CONF_VARS["BE"]["unzip"]["list-zip"]["split_char"] = " ";
$TYPO3_CONF_VARS["BE"]["unzip"]["list-zip"]["file_pos"] = 3;

$TYPO3_CONF_VARS["BE"]["unzip"]["unzip"]["pre_lines"] = 1;
$TYPO3_CONF_VARS["BE"]["unzip"]["unzip"]["post_lines"] = 0;
$TYPO3_CONF_VARS["BE"]["unzip"]["unzip"]["split_char"] = ":";
$TYPO3_CONF_VARS["BE"]["unzip"]["unzip"]["file_pos"] = 1;

$TYPO3_CONF_VARS["BE"]["unzip"]["unrar"]["pre_lines"] = -1;
$TYPO3_CONF_VARS["BE"]["unzip"]["unrar"]["post_lines"] = 0;
$TYPO3_CONF_VARS["BE"]["unzip"]["unrar"]["split_char"] = " ";
$TYPO3_CONF_VARS["BE"]["unzip"]["unrar"]["file_pos"] = 1;

$TYPO3_CONF_VARS["BE"]["unzip"]["rar"]["pre_lines"] = -1;
$TYPO3_CONF_VARS["BE"]["unzip"]["rar"]["post_lines"] = 0;
$TYPO3_CONF_VARS["BE"]["unzip"]["rar"]["split_char"] = " ";
$TYPO3_CONF_VARS["BE"]["unzip"]["rar"]["file_pos"] = 1;

$TYPO3_CONF_VARS["BE"]["unzip"]["zip"]["pre_lines"] = 0;
$TYPO3_CONF_VARS["BE"]["unzip"]["zip"]["split_char"] = ":";
$TYPO3_CONF_VARS["BE"]["unzip"]["zip"]["file_pos"] = 1;

$_EXTCONF = unserialize($_EXTCONF);    // unserializing the configuration so we can use it here
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$_EXTKEY]['dirTarget'] = trim($_EXTCONF['dirTarget']);
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$_EXTKEY]['fileTarget'] = trim($_EXTCONF['fileTarget']);
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$_EXTKEY]['targetExt'] = trim($_EXTCONF['targetExt']);
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$_EXTKEY]['DAMsupport'] = trim($_EXTCONF['DAMsupport']);


?>
