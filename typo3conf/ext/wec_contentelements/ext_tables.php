<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

$extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['wec_contentelements']);
if (is_array($extConf) && array_key_exists('includeDefaultContentElements', $extConf)) {
	$includeDefaultContentElements = $extConf['includeDefaultContentElements'];
} else {
	$includeDefaultContentElements = TRUE;
}

if ($includeDefaultContentElements) {
	tx_weccontentelements_lib::addContentElement($_EXTKEY, 'vimeo');
	tx_weccontentelements_lib::addContentElement($_EXTKEY, 'youtube');
	tx_weccontentelements_lib::addContentElement($_EXTKEY, 'localmenu');
	tx_weccontentelements_lib::addContentElement($_EXTKEY, 'slideshow');
	tx_weccontentelements_lib::addContentElement($_EXTKEY, 'filedownload');
}

?>
