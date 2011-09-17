<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

require_once(t3lib_extMgm::extPath($_EXTKEY).'class.tx_kbpackman_cm1.php');

if (TYPO3_MODE=='BE')	{
	$GLOBALS['TBE_MODULES_EXT']['xMOD_alt_clickmenu']['extendCMclasses'][]=array(
		'name' => 'tx_kbpackman_cm1',
		'path' => t3lib_extMgm::extPath($_EXTKEY).'class.tx_kbpackman_cm1.php'
	);

	if ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$_EXTKEY]['DAMsupport'] && t3lib_extMgm::isLoaded('dam') && t3lib_extMgm::isLoaded('dam_index') )	{
		t3lib_extMgm::insertModuleFunction(
			'tx_kbpackman',
			'tx_kbpackman_dam',
			t3lib_extMgm::extPath($_EXTKEY).'class.tx_kbpackman_dam.php',
			'LLL:EXT:dam_index/modfunc_index/locallang.xml:tx_damindex_index.title'
		);
	}

}
?>
