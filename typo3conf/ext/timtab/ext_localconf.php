<?php
//
//	$Id: ext_localconf.php,v 1.13 2005/10/30 11:46:02 ingorenner Exp $
//

if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

//get EXT path
$PATH_timtab = t3lib_extMgm::extPath('timtab');

if (TYPO3_MODE == 'FE')	{
	require_once($PATH_timtab.'class.tx_timtab_fe.php');
	require_once($PATH_timtab.'class.tx_timtab_catmenu.php');
} else {
	require_once($PATH_timtab.'class.tx_timtab_be.php');
}

//presetting userTS
t3lib_extMgm::addUserTSConfig('options.saveDocNew.tx_timtab_blogroll = 1');

//Extending TypoScript from static template uid=43 to set up userdefined tag:
t3lib_extMgm::addTypoScript($_EXTKEY,'editorcfg','tt_content.CSS_editor.ch.tx_timtab_pi1 = < plugin.tx_timtab_pi1.CSS_editor',43);

//listing Blogroll Links in Web->Page view
$TYPO3_CONF_VARS['EXTCONF']['cms']['db_layout']['addTables']['tx_timtab_blogroll'][0] = array(
	'fList' => 'name,url',
	'icon' => true
);

//adding plugins
t3lib_extMgm::addPItoST43($_EXTKEY,'pi1/class.tx_timtab_pi1.php','_pi1','list_type',1);
t3lib_extMgm::addPItoST43($_EXTKEY,'pi2/class.tx_timtab_pi2.php','_pi2','list_type',0);
t3lib_extMgm::addPItoST43($_EXTKEY,'pi3/class.tx_timtab_pi3.php','_pi3','list_type',1);

//registering for several hooks
$TYPO3_CONF_VARS['EXTCONF']['tt_news']['extraItemMarkerHook'][]        = 'tx_timtab_fe';
$TYPO3_CONF_VARS['EXTCONF']['tt_news']['userDisplayCatmenuHook'][]     = 'tx_timtab_catmenu';
$TYPO3_CONF_VARS['EXTCONF']['ve_guestbook']['extraItemMarkerHook'][]   = 'tx_timtab_fe';
$TYPO3_CONF_VARS['EXTCONF']['ve_guestbook']['preEntryInsertHook'][]    = 'tx_timtab_fe';
$TYPO3_CONF_VARS['EXTCONF']['ve_guestbook']['postEntryInsertedHook'][] = 'tx_timtab_fe';
$TYPO3_CONF_VARS['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] = 'tx_timtab_be'; 

?>