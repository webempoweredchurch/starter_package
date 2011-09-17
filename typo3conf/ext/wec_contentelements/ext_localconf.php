<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

$TYPO3_CONF_VARS['SC_OPTIONS']['tslib/class.tslib_content.php']['cObjTypeAndClassDefault'][] = 'tx_weccontentelements_cobj';
$TYPO3_CONF_VARS['SC_OPTIONS']['tslib/class.tslib_content.php']['getData'][] = 'tx_weccontentelements_getXMLData';

$extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['wec_contentelements']);
if (is_array($extConf) && array_key_exists('includeDefaultContentElements', $extConf)) {
	$includeDefaultContentElements = $extConf['includeDefaultContentElements'];
} else {
	$includeDefaultContentElements = TRUE;
}

if ($includeDefaultContentElements) {
	tx_weccontentelements_lib::addTyposcript($_EXTKEY, 'youtube');
	tx_weccontentelements_lib::addTyposcript($_EXTKEY, 'vimeo');
	tx_weccontentelements_lib::addTyposcript($_EXTKEY, 'localmenu');
	tx_weccontentelements_lib::addTyposcript($_EXTKEY, 'slideshow');
	tx_weccontentelements_lib::addTyposcript($_EXTKEY, 'filedownload');
}

?>
