<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');
t3lib_extMgm::addPItoST43($_EXTKEY,'pi2/class.tx_wecebible_pi2.php','_pi2','list_type',0);

$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['sr_feuser_register']['extendingTCA'][] = $_EXTKEY;

?>