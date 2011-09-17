<?php
if (!defined ("TYPO3_MODE")) die ("Access denied.");
if (TYPO3_branch>4.1) {
	// TYPO3 v4.2 or higher
	$TYPO3_CONF_VARS['SC_OPTIONS']['tslib/class.tslib_content.php']['stdWrap'][] = 'EXT:pmkfdl/class.tx_pmkfdl_hook.php:&tx_pmkfdl_hook';
}
else {
	require_once(t3lib_extMgm::extPath('pmkfdl').'class.tx_pmkfdl.php');
}
$TYPO3_CONF_VARS['FE']['eID_include']['pmkfdl'] = 'EXT:pmkfdl/class.tx_pmkfdl_download.php';

$conf = unserialize($_EXTCONF);
if ($conf['enableExampleHook']) {
	// Load and activate example hook
	require_once(t3lib_extMgm::extPath($_EXTKEY).'res/class.tx_pmkfdl_process_hook.php');
	$TYPO3_CONF_VARS['EXTCONF']['pmkfdl']['postProcessHook'][] = 'tx_pmkfdl_process_hook->postProcessHook'; 
	$TYPO3_CONF_VARS['EXTCONF']['pmkfdl']['preProcessHook'][] = 'tx_pmkfdl_process_hook->preProcessHook'; 
}
if ($conf['ttnewsFileHook'] && t3lib_extMgm::isLoaded('tt_news')) {
//	require_once(t3lib_extMgm::extPath($_EXTKEY).'class.tx_pmkfdl_ttnews_filemarkers.php');
//	$TYPO3_CONF_VARS['EXTCONF']['tt_news']['extraItemMarkerHook'][] = 'tx_pmkfdl_ttnews_filemarkers'; 
	$TYPO3_CONF_VARS['EXTCONF']['tt_news']['extraItemMarkerHook'][] = 'EXT:pmkfdl/res/class.tx_pmkfdl_ttnews_filemarkers.php:tx_pmkfdl_ttnews_filemarkers'; 
}
?>
