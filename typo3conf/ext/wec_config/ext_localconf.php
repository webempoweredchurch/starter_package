<?php
$conf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['wec_config']);
	
	// Register the RealURL autoconfiguration.
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/realurl/class.tx_realurl_autoconfgen.php']['extensionConfiguration']['wec_config'] = 'EXT:wec_config/realurl/class.tx_wecconfig_realurl.php:&tx_wecconfig_realurl->addRealURLConfig';

	// If be_acl is loaded, hide its records from list view.
if ($conf['tcaAdditions']) {
	if (t3lib_extMgm::isLoaded('be_acl')) {
		t3lib_extMgm::addPageTSConfig('mod.web_list.hideTables := addToList(tx_beacl_acl)');
	}

	if (t3lib_extMgm::isLoaded('templavoila')) {
		// Delete templavoila elements rather than unlinking them. The unset of User TSConfig is needed to allow changes via Page TSConfig.
		t3lib_extMgm::addPageTSConfig('
			mod.web_txtemplavoilaM1.enableDeleteIconForLocalElements = 2
			mod.web_txtemplavoilaM1.blindIcons = browse
			templavoila.wizards.newContentElement.renderMode = tabs
		');
		 t3lib_extMgm::addUserTSConfig('
			mod.web_txtemplavoilaM1.enableDeleteIconForLocalElements >
		 ');
	}
}

?>