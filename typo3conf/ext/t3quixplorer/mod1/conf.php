<?php

	// DO NOT REMOVE OR CHANGE THESE 3 LINES:
define('TYPO3_MOD_PATH', '../typo3conf/ext/t3quixplorer/mod1/');
$BACK_PATH='../../../../typo3/';

if (isset($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['t3quixplorer'])) {
	$tmp_config = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['t3quixplorer']);
} else {
	$tmp_config = array("allow_nonadmins" => 0);
}

if($tmp_config['allow_nonadmins'] == 1){
	$MCONF['name']='user_txt3quixplorerM1';
	$MCONF['access']='user,group';
} else {	
	$MCONF['name']='tools_txt3quixplorerM1';
	$MCONF['access']='admin';
}
	

$MCONF['script']='index.php';

$MLANG['default']['tabs_images']['tab'] = 'moduleicon.gif';
$MLANG['default']['ll_ref']='LLL:EXT:t3quixplorer/mod1/locallang_mod.php';
?>