<?php
if (!defined ("TYPO3_MODE")) 	die ("Access denied.");
t3lib_extMgm::addUserTSConfig('
	options.saveDocNew.tx_wecflashpresentation_class=1
');

/*
  ## Extending TypoScript from static template uid=43 to set up userdefined tag:
t3lib_extMgm::addTypoScript($_EXTKEY,"editorcfg","
	tt_content.CSS_editor.ch.tx_wecflashpresentation_pi1 = < plugin.tx_wecflashpresentation_pi1.CSS_editor
",43);
*/

t3lib_extMgm::addPItoST43($_EXTKEY,"pi1/class.tx_wecflashpresentation_pi1.php","_pi1","list_type",1);
t3lib_extMgm::addPItoST43($_EXTKEY,"pi2/class.tx_wecflashpresentation_pi2.php","_pi2","list_type",1);
?>